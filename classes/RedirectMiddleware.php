<?php

namespace Vdlp\Redirect\Classes;

use Vdlp\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Vdlp\Redirect\Models\Settings;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Log;

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
     * @throws \Cms\Classes\CmsException
     */
    public function handle($request, Closure $next)
    {
        // Only handle specific request methods
        if (!in_array($request->method(), ['GET', 'POST', 'HEAD'], true)) {
            return $next($request);
        }

        // Create the redirect manager if redirect rules are readable.
        $manager = new RedirectManager();
        $manager->setLoggingEnabled(Settings::isLoggingEnabled())
            ->setStatisticsEnabled(Settings::isStatisticsEnabled());

        if ($request->header('X-Adrenth-Redirect') === 'Tester') {
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
            Log::error("Could not perform redirect for $requestUri: " . $e->getMessage());
        }

        if ($rule) {
            $manager->redirectWithRule($rule, $requestUri);
        }

        return $next($request);
    }
}
