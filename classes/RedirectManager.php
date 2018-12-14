<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Carbon\Carbon;
use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Exception;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use InvalidArgumentException;
use League\Csv\Reader;
use Symfony\Component\Routing;
use Throwable;
use Vdlp\Redirect\Classes\Contracts\RedirectManagerInterface;
use Vdlp\Redirect\Classes\Exceptions;
use Vdlp\Redirect\Models;

/**
 * Class RedirectManager
 *
 * @SuppressWarnings(PHPMD.ExitExpression)
 * @package Vdlp\Redirect\Classes
 */
class RedirectManager implements RedirectManagerInterface
{
    /**
     * The redirect rules which this manager uses to perform matching.
     *
     * @var RedirectRule[]
     */
    private $redirectRules;

    /**
     * The date for which the matching should be done.
     *
     * @var Carbon
     */
    private $matchDate;

    /**
     * Site base path.
     *
     * @var string
     */
    private $basePath;

    /**
     * Whether redirect logging is enabled (default: true).
     *
     * @var bool
     */
    private $loggingEnabled = true;

    /**
     * Whether the manager should gather statistics (default: true).
     * @var bool
     */
    private $statisticsEnabled = true;

    /**
     * HTTP 1.1 headers
     *
     * @var array
     */
    private static $headers = [
        301 => 'HTTP/1.1 301 Moved Permanently',
        302 => 'HTTP/1.1 302 Found',
        303 => 'HTTP/1.1 303 See Other',
        404 => 'HTTP/1.1 404 Not Found',
        410 => 'HTTP/1.1 410 Gone',
    ];

    /**
     * Constructs a RedirectManager instance.
     */
    public function __construct()
    {
        /** @var Request $request */
        $request = resolve(Request::class);

        $this->matchDate = Carbon::today();
        $this->basePath = $request->getBasePath();
    }

    /**
     * Create an instance of the RedirectManager with a redirect rule.
     *
     * @param RedirectRule $rule
     * @return RedirectManager
     */
    public static function createWithRule(RedirectRule $rule): RedirectManagerInterface
    {
        $instance = new self();
        $instance->redirectRules[] = $rule;
        return $instance;
    }

    /**
     * Find a match based on given URL.
     *
     * @param string $requestPath
     * @param string $scheme 'http' or 'https'
     * @return RedirectRule|false
     * @throws Exceptions\InvalidScheme
     */
    public function match(string $requestPath, string $scheme)
    {
        if ($scheme !== Models\Redirect::SCHEME_HTTP && $scheme !== Models\Redirect::SCHEME_HTTPS) {
            throw Exceptions\InvalidScheme::withScheme($scheme);
        }

        $requestPath = urldecode($requestPath);

        $this->loadRedirectRules();

        foreach ($this->redirectRules as $rule) {
            $matchedRule = $this->matchesRule($rule, $requestPath, $scheme);

            if ($matchedRule) {
                return $matchedRule;
            }
        }

        return false;
    }

    /**
     * Find a match based on given URL (uses caching).
     *
     * @param string $requestPath
     * @param string $scheme 'http' or 'https'
     * @return RedirectRule|false|mixed
     * @throws Exceptions\InvalidScheme
     */
    public function matchCached(string $requestPath, string $scheme)
    {
        $cacheManager = CacheManager::instance();
        $cacheKey = $cacheManager->cacheKey($requestPath, $scheme);

        if ($cacheManager->has($cacheKey)) {
            $cachedItem = $cacheManager->get($cacheKey);

            // Verify the data from cache. In some cases a cache driver can not unserialize
            // (due to invalid php configuration) the cached data which causes this function to return invalid data.
            //
            // E.g. memcache:
            // - Memcached::get(): could not unserialize value, no igbinary support in ...
            // - Memcached::get(): could not unserialize value, no msgpack support in ...
            if ($cachedItem === false || $cachedItem instanceof RedirectRule) {
                return $cachedItem;
            }
        }

        $matchedRule = $this->match($requestPath, $scheme);

        return $cacheManager->putMatch($cacheKey, $matchedRule);
    }

    /**
     * Redirect with specific rule.
     *
     * @param RedirectRule $rule
     * @param string $requestUri
     * @return void
     * @throws \Cms\Classes\CmsException
     */
    public function redirectWithRule(RedirectRule $rule, string $requestUri)//: void
    {
        if ($this->statisticsEnabled) {
            $helper = new StatisticsHelper();
            $helper->increaseHitsForRedirect($rule->getId());
        }

        $statusCode = $rule->getStatusCode();

        if ($statusCode === 404 || $statusCode === 410) {
            header(self::$headers[$statusCode], true, $statusCode);
            $this->addLogEntry($rule, $requestUri, '');
            exit(0);
        }

        $toUrl = $this->getLocation($rule);

        if (!$toUrl
            || empty($toUrl)
            || (new \Cms\Helpers\Cms())->url($requestUri) === $toUrl // Prevent redirect loops
        ) {
            return;
        }

        $this->addLogEntry($rule, $requestUri, $toUrl);

        header(self::$headers[$statusCode], true, $statusCode);
        header('X-Redirect-By: Vdlp.Redirect');
        header('Location: ' . $toUrl, true, $statusCode);

        exit(0);
    }

    /**
     * Get Location URL to redirect to.
     *
     * @param RedirectRule $rule
     * @return bool|string
     * @throws \Cms\Classes\CmsException
     */
    public function getLocation(RedirectRule $rule)
    {
        $toUrl = false;

        // Determine the URL to redirect to
        switch ($rule->getTargetType()) {
            case Models\Redirect::TARGET_TYPE_PATH_URL:
                $toUrl = $this->redirectToPathOrUrl($rule);

                // Check if $toUrl is a relative path, if so, we need to add the base path to it.
                // Refs: https://github.com/vdlp/redirect/issues/21
                if (is_string($toUrl)
                    && $toUrl[0] !== '/'
                    && strpos($toUrl, 'http://') !== 0
                    && strpos($toUrl, 'https://') !== 0
                ) {
                    $toUrl = $this->basePath . '/' . $toUrl;
                }

                if (strpos($toUrl, '/') === 0) {
                    $toUrl = (new \Cms\Helpers\Cms())->url($toUrl);
                }

                break;
            case Models\Redirect::TARGET_TYPE_CMS_PAGE:
                $toUrl = $this->redirectToCmsPage($rule);
                break;
            case Models\Redirect::TARGET_TYPE_STATIC_PAGE:
                $toUrl = $this->redirectToStaticPage($rule);
                break;
        }

        if ($rule->getToScheme() !== Models\Redirect::SCHEME_AUTO) {
            $toUrl = str_replace(['https://', 'http://'], $rule->getToScheme() . '://', $toUrl);
        }

        return $toUrl;
    }

    /**
     * Enable or disable logging.
     *
     * @param bool $loggingEnabled
     * @return RedirectManager
     */
    public function setLoggingEnabled(bool $loggingEnabled): RedirectManager
    {
        $this->loggingEnabled = $loggingEnabled;
        return $this;
    }

    /**
     * Enable or disable gathering of statistics.
     *
     * @param bool $statisticsEnabled
     * @return RedirectManager
     */
    public function setStatisticsEnabled(bool $statisticsEnabled): RedirectManager
    {
        $this->statisticsEnabled = $statisticsEnabled;
        return $this;
    }

    /**
     * @param string $basePath
     * @return RedirectManager
     */
    public function setBasePath(string $basePath): RedirectManager
    {
        $this->basePath = rtrim($basePath, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Change the match date; can be used to perform tests.
     *
     * @param Carbon $matchDate
     * @return RedirectManager
     */
    public function setMatchDate(Carbon $matchDate): RedirectManager
    {
        $this->matchDate = $matchDate;
        return $this;
    }

    /**
     * @param RedirectRule $rule
     * @return string
     */
    private function redirectToPathOrUrl(RedirectRule $rule): string
    {
        if ($rule->isExactMatchType()) {
            return $rule->getToUrl();
        }

        $placeholderMatches = $rule->getPlaceholderMatches();

        return str_replace(
            array_keys($placeholderMatches),
            array_values($placeholderMatches),
            $rule->getToUrl()
        );
    }

    /**
     * @param RedirectRule $rule
     * @return string
     * @throws \Cms\Classes\CmsException
     */
    private function redirectToCmsPage(RedirectRule $rule): string
    {
        $controller = new Controller(Theme::getActiveTheme());

        $parameters = [];

        // Strip curly braces from keys
        foreach ($rule->getPlaceholderMatches() as $placeholder => $value) {
            $parameters[str_replace(['{', '}'], '', $placeholder)] = $value;
        }

        return (string) $controller->pageUrl($rule->getCmsPage(), $parameters);
    }

    /**
     * @param RedirectRule $rule
     * @return string|bool
     */
    private function redirectToStaticPage(RedirectRule $rule)
    {
        if (class_exists('\RainLab\Pages\Classes\Page')) {
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            /** @noinspection PhpUndefinedClassInspection */
            return \RainLab\Pages\Classes\Page::url($rule->getStaticPage());
        }

        return false;
    }

    /**
     * Check if rule matches against request path and scheme.
     *
     * @param RedirectRule $rule
     * @param string $requestPath
     * @param string $scheme
     * @return RedirectRule|bool
     */
    private function matchesRule(RedirectRule $rule, string $requestPath, string $scheme)
    {
        if (!$this->matchesScheme($rule, $scheme)
            || !$this->matchesPeriod($rule)
        ) {
            return false;
        }

        // Strip query parameters from request path.
        if ($rule->isIgnoreQueryParameters()) {
            $requestPath = parse_url($requestPath, PHP_URL_PATH);
        }

        // Perform exact match if applicable.
        if ($rule->isExactMatchType()) {
            return $this->matchExact($rule, $requestPath);
        }

        // Perform placeholders match if applicable.
        if ($rule->isPlaceholdersMatchType()) {
            return $this->matchPlaceholders($rule, $requestPath);
        }

        return false;
    }

    /**
     * Perform an exact URL match.
     *
     * @param RedirectRule $rule
     * @param string $url
     * @return RedirectRule|bool
     */
    private function matchExact(RedirectRule $rule, string $url)
    {
        return $url === $rule->getFromUrl() ? $rule : false;
    }

    /**
     * Perform a placeholder URL match.
     *
     * @param RedirectRule $rule
     * @param string $url
     * @return RedirectRule|bool
     */
    private function matchPlaceholders(RedirectRule $rule, string $url)
    {
        $route = new Routing\Route($rule->getFromUrl());

        foreach ($rule->getRequirements() as $requirement) {
            try {
                $route->setRequirement(
                    str_replace(['{', '}'], '', $requirement['placeholder']),
                    $requirement['requirement']
                );
            } catch (InvalidArgumentException $e) {
                // Catch empty requirement / placeholder
            }
        }

        $routeCollection = new Routing\RouteCollection();
        $routeCollection->add($rule->getId(), $route);

        try {
            $matcher = new Routing\Matcher\UrlMatcher(
                $routeCollection,
                new Routing\RequestContext('/')
            );

            $match = $matcher->match($url);

            $items = array_except($match, '_route');

            foreach ($items as $key => $value) {
                $placeholder = '{' . $key . '}';
                $replacement = $this->findReplacementForPlaceholder($rule, $placeholder);
                $items[$placeholder] = $replacement ?? $value;
                unset($items[$key]);
            }

            $rule->setPlaceholderMatches($items);
        } catch (Exception $e) {
            return false;
        }

        return $rule;
    }

    /**
     * Check if rule matches a period.
     *
     * @param RedirectRule $rule
     * @return bool
     */
    private function matchesPeriod(RedirectRule $rule): bool
    {
        if ($rule->getFromDate() instanceof Carbon
            && $rule->getToDate() instanceof Carbon
        ) {
            return $this->matchDate->between($rule->getFromDate(), $rule->getToDate());
        }

        if ($rule->getFromDate() instanceof Carbon
            && $rule->getToDate() === null
        ) {
            return $this->matchDate->gte($rule->getFromDate());
        }

        if ($rule->getToDate() instanceof Carbon
            && $rule->getFromDate() === null
        ) {
            return $this->matchDate->lte($rule->getToDate());
        }

        return true;
    }

    /**
     * @param RedirectRule $rule
     * @param string $scheme
     * @return bool
     */
    private function matchesScheme(RedirectRule $rule, string $scheme): bool
    {
        if ($rule->getFromScheme() === Models\Redirect::SCHEME_AUTO) {
            return true;
        }

        return $rule->getFromScheme() === $scheme;
    }

    /**
     * Find replacement value for placeholder.
     *
     * @param RedirectRule $rule
     * @param string $placeholder
     * @return string|null
     */
    private function findReplacementForPlaceholder(RedirectRule $rule, string $placeholder)//: ?string
    {
        foreach ($rule->getRequirements() as $requirement) {
            if ($requirement['placeholder'] === $placeholder && !empty($requirement['replacement'])) {
                return (string) $requirement['replacement'];
            }
        }

        return null;
    }

    /**
     * Load definitions into memory.
     *
     * @return void
     */
    private function loadRedirectRules()//: void
    {
        if ($this->redirectRules !== null) {
            return;
        }

        $rules = [];

        try {
            if (CacheManager::cachingEnabledAndSupported()) {
                $rules = $this->readRulesFromCache();
            } else {
                $rules = $this->readRulesFromFilesystem();
            }
        } catch (Throwable $e) {
            $logger = resolve(Log::class);
            $logger->error($e);
        }

        $this->redirectRules = $rules;
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     * @throws Exceptions\RulesPathNotReadable
     */
    private function readRulesFromFilesystem(): array
    {
        $rules = [];

        $rulesPath = storage_path('app/redirects.csv');

        if (!file_exists($rulesPath) || !is_readable($rulesPath)) {
            throw Exceptions\RulesPathNotReadable::withPath($rulesPath);
        }

        /** @var Reader $reader */
        $reader = Reader::createFromPath($rulesPath);

        // WARNING: this is deprecated method in league/csv:8.0, when league/csv is upgraded to version 9 we should
        // follow the instructions on this page: http://csv.thephpleague.com/upgrading/9.0/
        $results = $reader->fetchAssoc(0);

        foreach ($results as $row) {
            $rule = new RedirectRule($row);

            if ($this->matchesPeriod($rule)) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * @return array
     */
    private function readRulesFromCache(): array
    {
        $results = CacheManager::instance()->getRedirectRules();

        $rules = [];

        foreach ($results as $row) {
            $rule = new RedirectRule($row);

            if ($this->matchesPeriod($rule)) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Adds a log entry to the database.
     *
     * @param RedirectRule $rule
     * @param string $requestUri
     * @param string $toUrl
     * @return void
     */
    private function addLogEntry(RedirectRule $rule, string $requestUri, string $toUrl)//: void
    {
        if (!$this->loggingEnabled) {
            return;
        }

        /** @var Models\Redirect $redirect */
        $redirect = Models\Redirect::find($rule->getId());

        if ($redirect === null) {
            return;
        }

        $now = Carbon::now();

        Models\RedirectLog::create([
            'redirect_id' => $rule->getId(),
            'from_url' => $requestUri,
            'to_url' => $toUrl,
            'status_code' => $rule->getStatusCode(),
            'day' => $now->day,
            'month' => $now->month,
            'year' => $now->year,
            'date_time' => $now,
        ]);
    }
}
