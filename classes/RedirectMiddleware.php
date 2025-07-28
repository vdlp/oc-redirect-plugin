<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use October\Rain\Events\Dispatcher;
use Psr\Log\LoggerInterface;
use Throwable;
use Vdlp\Redirect\Classes\Contracts\CacheManagerInterface;
use Vdlp\Redirect\Classes\Contracts\RedirectManagerInterface;
use Vdlp\Redirect\Classes\Exceptions\InvalidScheme;
use Vdlp\Redirect\Classes\Exceptions\NoMatchForRequest;
use Vdlp\Redirect\Classes\Exceptions\UnableToLoadRules;
use Vdlp\Redirect\Models\Settings;

final class RedirectMiddleware
{
    private static array $supportedMethods = ['GET', 'POST', 'HEAD'];

    public function __construct(
        private RedirectManagerInterface $redirectManager,
        private RedirectConditionManager $redirectConditionManager,
        private CacheManagerInterface $cacheManager,
        private Dispatcher $dispatcher,
        private LoggerInterface $log
    ) {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        // Only handle specific request methods.
        if (
            $request->isXmlHttpRequest()
            || !in_array($request->method(), self::$supportedMethods, true)
            || Str::startsWith($request->getRequestUri(), '/vdlp/redirect/sparkline/')
        ) {
            return $next($request);
        }

        if ($request->header('X-Vdlp-Redirect') === 'Tester') {
            $this->redirectManager->setSettings(new RedirectManagerSettings(
                false,
                false,
                Settings::isRelativePathsEnabled()
            ));
        }

        $rule = false;

        $requestUri = str_replace($request->getBasePath(), '', $request->getRequestUri());

        try {
            if (
                $this->cacheManager->cachingEnabledAndSupported()
                && method_exists($this->redirectManager, 'matchCached')
            ) {
                $rule = $this->redirectManager->matchCached($requestUri, $request->getScheme());
            } else {
                $rule = $this->redirectManager->match($requestUri, $request->getScheme());
            }
        } catch (NoMatchForRequest | UnableToLoadRules | InvalidScheme) {
            // @ignoreException
            $rule = false;
        } catch (Throwable $throwable) {
            $this->log->error(sprintf(
                'Vdlp.Redirect: Could not perform redirect for %s (scheme: %s): %s',
                $requestUri,
                $request->getScheme(),
                $throwable->getMessage()
            ));
        }

        if ($rule === false || $rule === null) {
            return $next($request);
        }

        /*
         * Extensibility:
         *
         * At this point a positive match was made based on the request URI.
         */
        $this->dispatcher->fire('vdlp.redirect.match', [$rule, $requestUri]);

        /*
         * Extensibility:
         *
         * Developers can add their own conditions. If a condition does not pass the redirect will be ignored.
         */
        foreach ($this->redirectConditionManager->getEnabledConditions($rule) as $condition) {
            if (!$condition->passes($rule, $requestUri)) {
                return $next($request);
            }
        }

        $this->redirectManager->redirectWithRule($rule, $requestUri);

        return $next($request);
    }
}
