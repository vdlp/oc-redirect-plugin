<?php

declare(strict_types=1);

namespace Vdlp\Redirect\ServiceProviders;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use October\Rain\Events\Dispatcher;
use October\Rain\Support\ServiceProvider;
use Vdlp\Redirect\Classes\Contracts\RedirectManagerInterface;
use Vdlp\Redirect\Classes\RedirectManager;

/**
 * Class RedirectManager
 *
 * @package Vdlp\Redirect\ServiceProviders
 */
class Redirect extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(RedirectManager::class, static function (Container $container) {
            return new RedirectManager(
                $container->make(Request::class),
                $container->make(Dispatcher::class)
            );
        });

        $this->app->alias(RedirectManager::class, RedirectManagerInterface::class);
    }
}
