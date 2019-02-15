<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Closure;
use Exception;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use October\Rain\Events\Dispatcher;
use Vdlp\Redirect\Classes\Contracts\RedirectConditionInterface;
use Vdlp\Redirect\Classes\Contracts\RedirectManagerInterface;
use Vdlp\Redirect\Models\Settings;

/**
 * Class RedirectMiddleware
 *
 * @package Vdlp\Redirect\Classes
 */
class RedirectMiddleware
{
    /**
     * Run the request filter.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Only handle specific request methods.
        if (!in_array($request->method(), ['GET', 'POST', 'HEAD'], true)) {
            return $next($request);
        }

        /** @var RedirectManagerInterface $manager */
        $manager = resolve(RedirectManagerInterface::class);
        $manager->setLoggingEnabled(Settings::isLoggingEnabled())
            ->setStatisticsEnabled(Settings::isStatisticsEnabled());

        if ($request->header('X-Vdlp-Redirect') === 'Tester') {
            $manager->setStatisticsEnabled(false)
                ->setLoggingEnabled(false);
        }

        $rule = false;

        $requestUri = str_replace($request->getBasePath(), '', $request->getRequestUri());

        try {
            if (CacheManager::cachingEnabledAndSupported()) {
                $rule = $manager->matchCached($requestUri, $request->getScheme());
            } else {
                $rule = $manager->match($requestUri, $request->getScheme());
            }
        } catch (Exception $e) {
            $logger = resolve(Log::class);
            $logger->error("Vdlp.Redirect: Could not perform redirect for $requestUri: " . $e->getMessage());
        }

        if (!$rule) {
            return $next($request);
        }

        /** @var Dispatcher $eventDispatcher */
        $eventDispatcher = resolve(Dispatcher::class);

        /*
         * Extensibility:
         *
         * At this point a positive match was made based on the request URI.
         */
        $eventDispatcher->fire('vdlp.redirect.match', [$rule, $requestUri]);

        /*
         * Extensibility:
         *
         * Developers can add their own conditions. If a condition does not pass the redirect will be ignored.
         */
        foreach ($manager->getConditions() as $condition) {
            /** @var RedirectConditionInterface $condition */
            $condition = app($condition);

            if (!$condition->passes($rule, $requestUri)) {
                return $next($request);
            }
        }

        $manager->redirectWithRule($rule, $requestUri);
    }
}
