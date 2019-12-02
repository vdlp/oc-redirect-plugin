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

/**
 * Class RedirectMiddleware
 *
 * @package Vdlp\Redirect\Classes
 */
class RedirectMiddleware
{
    /**
     * @var RedirectManagerInterface
     */
    private $redirectManager;

    /**
     * @param RedirectManagerInterface $redirectManager
     */
    public function __construct(RedirectManagerInterface $redirectManager)
    {
        $this->redirectManager = $redirectManager;
    }

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

        if ($request->header('X-Vdlp-Redirect') === 'Tester') {
            $this->redirectManager->setSettings(new RedirectManagerSettings(false, false));
        }

        $rule = false;

        $requestUri = str_replace($request->getBasePath(), '', $request->getRequestUri());

        try {
            if (CacheManager::cachingEnabledAndSupported()) {
                $rule = $this->redirectManager->matchCached($requestUri, $request->getScheme());
            } else {
                $rule = $this->redirectManager->match($requestUri, $request->getScheme());
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
        foreach ($this->redirectManager->getConditions() as $condition) {
            /** @var RedirectConditionInterface $condition */
            $condition = app($condition);

            if (!$condition->passes($rule, $requestUri)) {
                return $next($request);
            }
        }

        $this->redirectManager->redirectWithRule($rule, $requestUri);

        return $next($request);
    }
}
