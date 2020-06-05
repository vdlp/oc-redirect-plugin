<?php

/** @noinspection EfferentObjectCouplingInspection */

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Carbon\Carbon;
use Cms\Classes\CmsException;
use Cms\Classes\Controller;
use Cms\Classes\Router;
use Cms\Classes\Theme;
use Cms\Helpers\Cms;
use Illuminate\Http\Request;
use League\Csv\Reader;
use RuntimeException;
use Symfony\Component\Routing;
use Throwable;
use Vdlp\Redirect\Classes\Contracts\CacheManagerInterface;
use Vdlp\Redirect\Classes\Contracts\RedirectConditionInterface;
use Vdlp\Redirect\Classes\Contracts\RedirectManagerInterface;
use Vdlp\Redirect\Classes\Exceptions;
use Vdlp\Redirect\Classes\Util\Str;
use Vdlp\Redirect\Models;

final class RedirectManager implements RedirectManagerInterface
{
    /**
     * The redirect rules which this manager uses to perform matching.
     *
     * @var RedirectRule[]
     */
    private $rules;

    /**
     * @var RedirectConditionInterface[]
     */
    private $conditions = [];

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
     * @var RedirectManagerSettings
     */
    private $settings;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

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
     * @var array
     */
    private static $schemes = [
        Models\Redirect::SCHEME_HTTP,
        Models\Redirect::SCHEME_HTTPS,
    ];

    public function __construct(Request $request, CacheManagerInterface $cacheManager)
    {
        $this->matchDate = Carbon::today();
        $this->basePath = $request->getBasePath();
        $this->settings = RedirectManagerSettings::createDefault();
        $this->cacheManager = $cacheManager;
    }

    public static function createWithRule(RedirectRule $rule): RedirectManagerInterface
    {
        $instance = new self(
            resolve(Request::class),
            resolve(CacheManagerInterface::class)
        );

        $instance->rules[] = $rule;

        return $instance;
    }

    /**
     * {@inheritDoc}
     * @throws Exceptions\InvalidScheme
     * @throws Exceptions\NoMatchForRequest
     * @throws Exceptions\UnableToLoadRules
     */
    public function match(string $requestPath, string $scheme): RedirectRule
    {
        if (!in_array($scheme, self::$schemes, true)) {
            throw Exceptions\InvalidScheme::withScheme($scheme);
        }

        $requestPath = urldecode($requestPath);

        $this->loadRedirectRules();

        foreach ($this->rules as $rule) {
            try {
                return $this->matchesRule($rule, $requestPath, $scheme);
            } catch (Exceptions\NoMatchForRule $e) {
                continue;
            }
        }

        throw Exceptions\NoMatchForRequest::withRequestPath($requestPath, $scheme);
    }

    public function matchCached(string $requestPath, string $scheme)
    {
        $cacheKey = $this->cacheManager->cacheKey($requestPath, $scheme);

        if ($this->cacheManager->has($cacheKey)) {
            $cachedItem = $this->cacheManager->get($cacheKey);

            // Verify the data from cache. In some cases a cache driver can not unserialize
            // (due to invalid php configuration) the cached data which causes this function to return invalid data.
            //
            // E.g. memcache:
            // - Memcached::get(): could not unserialize value, no igbinary support in ...
            // - Memcached::get(): could not unserialize value, no msgpack support in ...
            if ($cachedItem instanceof RedirectRule) {
                return $cachedItem;
            }
        }

        try {
            $matchedRule = $this->match($requestPath, $scheme);
        } catch (Exceptions\NoMatchForRequest | Exceptions\InvalidScheme | Exceptions\UnableToLoadRules $e) {
            $matchedRule = null;
        }

        return $this->cacheManager->putMatch($cacheKey, $matchedRule);
    }

    /**
     * @throws CmsException
     */
    public function redirectWithRule(RedirectRule $rule, string $requestUri): void
    {
        if ($this->settings->isStatisticsEnabled()) {
            (new StatisticsHelper())->increaseHitsForRedirect($rule->getId());
        }

        $statusCode = $rule->getStatusCode();

        if ($statusCode === 404 || $statusCode === 410) {
            header(self::$headers[$statusCode], true, $statusCode);
            $this->addLogEntry($rule, $requestUri, '');
            exit(0);
        }

        $toUrl = $this->getLocation($rule);

        $targetIsEqual = $this->settings->isRelativePathsEnabled()
            ? $requestUri === $toUrl
            : (new Cms)->url($requestUri) === $toUrl;

        if (!$toUrl
            || empty($toUrl)
            || $targetIsEqual // Prevent redirect loop
        ) {
            return;
        }

        $this->addLogEntry($rule, $requestUri, $toUrl);

        header(self::$headers[$statusCode], true, $statusCode);
        header('X-Redirect-By: Vdlp.Redirect');
        header('X-Redirect-Id: ' . $rule->getId());
        header('Cache-Control: no-store');
        header('Location: ' . $toUrl, true, $statusCode);

        exit(0);
    }

    /**
     * @throws CmsException
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
                    $toUrl = $this->settings->isRelativePathsEnabled()
                        ? $toUrl
                        : (new Cms())->url($toUrl);
                }

                break;
            case Models\Redirect::TARGET_TYPE_CMS_PAGE:
                $toUrl = $this->redirectToCmsPage($rule);
                break;
            case Models\Redirect::TARGET_TYPE_STATIC_PAGE:
                try {
                    $toUrl = $this->redirectToStaticPage($rule);
                } catch (Throwable $e) {
                    $toUrl = false;
                }
                break;
        }

        if ($rule->getToScheme() !== Models\Redirect::SCHEME_AUTO
            && (strpos($toUrl, 'http://') === 0 || strpos($toUrl, 'https://') === 0)
        ) {
            $toUrl = str_replace(['https://', 'http://'], $rule->getToScheme() . '://', $toUrl);
        }

        return $toUrl;
    }

    public function getConditions(): array
    {
        return array_keys($this->conditions);
    }

    public function addCondition(string $conditionClass, int $priority): RedirectManagerInterface
    {
        $this->conditions[$conditionClass] = $priority;
        arsort($this->conditions);
        return $this;
    }

    public function setSettings(RedirectManagerSettings $settings): RedirectManagerInterface
    {
        $this->settings = $settings;
        return $this;
    }

    public function setBasePath(string $basePath): RedirectManager
    {
        $this->basePath = rtrim($basePath, '/');
        return $this;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function setMatchDate(Carbon $matchDate): RedirectManager
    {
        $this->matchDate = $matchDate;
        return $this;
    }

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
     * @throws CmsException
     */
    private function redirectToCmsPage(RedirectRule $rule): string
    {
        $parameters = [];

        // Strip curly braces from keys
        foreach ($rule->getPlaceholderMatches() as $placeholder => $value) {
            $parameters[str_replace(['{', '}'], '', (string) $placeholder)] = $value;
        }

        if ($this->settings->isRelativePathsEnabled()) {
            $router = new Router(Theme::getActiveTheme());

            return $router->findByFile(
                $rule->getCmsPage(),
                array_merge($router->getParameters(), $parameters)
            );
        }

        return (string) (new Controller(Theme::getActiveTheme()))
            ->pageUrl($rule->getCmsPage(), $parameters);
    }

    /**
     * @throws RuntimeException
     * @noinspection ClassConstantCanBeUsedInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    private function redirectToStaticPage(RedirectRule $rule): string
    {
        if (!class_exists('\RainLab\Pages\Classes\Page')) {
            throw new RuntimeException('Cannot create URL to RainLab Page: Plugin not installed.');
        }

        /** @var \RainLab\Pages\Classes\Page $page */
        $page = \RainLab\Pages\Classes\Page::loadCached(
            Theme::getActiveTheme(),
            $rule->getStaticPage()
        );

        if ($page === null) {
            throw new RuntimeException('Cannot create URL to RainLab Page: Page not found.');
        }

        return $this->settings->isRelativePathsEnabled()
            ? (string) array_get($page->attributes, 'viewBag.url')
            : (string) \RainLab\Pages\Classes\Page::url($rule->getStaticPage());
    }

    /**
     * @throws Exceptions\NoMatchForRule
     */
    private function matchesRule(RedirectRule $rule, string $requestPath, string $scheme): RedirectRule
    {
        if (!$this->matchesScheme($rule, $scheme)
            || !$this->matchesPeriod($rule)
        ) {
            throw Exceptions\NoMatchForRule::withRedirectRule($rule, $requestPath, $scheme);
        }

        // Strip query parameters from request path.
        if ($rule->isIgnoreQueryParameters()) {
            $parseResult = parse_url($requestPath, PHP_URL_PATH);

            if (is_string($parseResult)) {
                $requestPath = $parseResult;
            }
        }

        // Perform exact match if applicable.
        if ($rule->isExactMatchType()) {
            return $this->matchExact($rule, $requestPath);
        }

        // Perform placeholders match if applicable.
        if ($rule->isPlaceholdersMatchType()) {
            return $this->matchPlaceholders($rule, $requestPath);
        }

        // Perform regex match if applicable.
        if ($rule->isRegexMatchType()) {
            return $this->matchRegex($rule, $requestPath);
        }

        throw Exceptions\NoMatchForRule::withRedirectRule($rule, $requestPath, $scheme);
    }

    /**
     * @throws Exceptions\NoMatchForRule
     */
    private function matchExact(RedirectRule $rule, string $url): RedirectRule
    {
        $urlA = $rule->getFromUrl();
        $urlB = $url;

        if ($rule->isIgnoreTrailingSlash()) {
            $urlA = Str::removeTrailingSlash($urlA);
            $urlB = Str::removeTrailingSlash($urlB);
        }

        if ($rule->isIgnoreCase() && strcasecmp($urlA, $urlB) === 0) {
            return $rule;
        }

        if ($urlA === $urlB) {
            return $rule;
        }

        throw Exceptions\NoMatchForRule::withRedirectRule($rule, $url);
    }

    /**
     * @throws Exceptions\NoMatchForRule
     */
    private function matchPlaceholders(RedirectRule $rule, string $url): RedirectRule
    {
        $route = new Routing\Route($rule->getFromUrl());

        foreach ($rule->getRequirements() as $requirement) {
            try {
                $route->setRequirement(
                    str_replace(['{', '}'], '', $requirement['placeholder']),
                    $requirement['requirement']
                );
            } catch (Throwable $e) {
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
        } catch (Throwable $e) {
            throw Exceptions\NoMatchForRule::withRedirectRule($rule, $url);
        }

        return $rule;
    }

    /**
     * @throws Exceptions\NoMatchForRule
     */
    private function matchRegex(RedirectRule $rule, string $url): RedirectRule
    {
        $pattern = $rule->getFromUrl();

        try {
            if (preg_match($pattern, $url) === 1) {
                return $rule;
            }
        } catch (Throwable $e) {
            // ..
        }

        throw Exceptions\NoMatchForRule::withRedirectRule($rule, $url);
    }

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

    private function findReplacementForPlaceholder(RedirectRule $rule, string $placeholder): ?string
    {
        foreach ($rule->getRequirements() as $requirement) {
            if ($requirement['placeholder'] === $placeholder && !empty($requirement['replacement'])) {
                return (string) $requirement['replacement'];
            }
        }

        return null;
    }

    /**
     * @throws Exceptions\UnableToLoadRules
     */
    private function loadRedirectRules(): void
    {
        if ($this->rules !== null) {
            return;
        }

        if ($this->cacheManager->cachingEnabledAndSupported()) {
            $rules = $this->loadRulesFromCache();
        } else {
            $rules = $this->loadRulesFromFilesystem();
        }

        $this->rules = $rules;
    }

    /**
     * @throws Exceptions\UnableToLoadRules
     */
    private function loadRulesFromFilesystem(): array
    {
        $rulesPath = (string) config('vdlp.redirect::rules_path');

        if (!file_exists($rulesPath) && touch($rulesPath) === false) {
            throw Exceptions\RulesPathNotWritable::withPath($rulesPath);
        }

        if (!is_readable($rulesPath)) {
            throw Exceptions\RulesPathNotReadable::withPath($rulesPath);
        }

        try {
            $reader = Reader::createFromPath($rulesPath, 'r');

            if (method_exists($reader, 'fetchAssoc')) {
                // Supports league/csv:8.0+
                $results = $reader->fetchAssoc(0);
            } else {
                // Supports league/csv:9.0+
                /** @noinspection PhpUndefinedMethodInspection */
                $reader->setHeaderOffset(0);

                /** @noinspection PhpUndefinedMethodInspection */
                $results = $reader->getRecords();
            }
        } catch (Throwable $e) {
            throw Exceptions\UnableToLoadRules::withMessage($e->getMessage(), $e);
        }

        $rules = [];

        foreach ($results as $row) {
            $rule = new RedirectRule($row);

            if ($this->matchesPeriod($rule)) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    private function loadRulesFromCache(): array
    {
        $results = $this->cacheManager->getRedirectRules();

        $rules = [];

        foreach ($results as $row) {
            $rule = new RedirectRule($row);

            if ($this->matchesPeriod($rule)) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    private function addLogEntry(RedirectRule $rule, string $requestUri, string $toUrl): void
    {
        if (!$this->settings->isLoggingEnabled()) {
            return;
        }

        /** @var Models\Redirect $redirect */
        $redirect = Models\Redirect::query()->find($rule->getId());

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
