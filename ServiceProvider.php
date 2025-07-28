<?php

declare(strict_types=1);

namespace Vdlp\Redirect;

use October\Rain\Support\ServiceProvider as ServiceProviderBase;
use TypeError;
use Vdlp\Redirect\Classes\CacheManager;
use Vdlp\Redirect\Classes\Contracts;
use Vdlp\Redirect\Classes\PublishManager;
use Vdlp\Redirect\Classes\RedirectManager;

final class ServiceProvider extends ServiceProviderBase
{
    /**
     * @throws TypeError
     */
    public function register(): void
    {
        $this->app->bind(Contracts\RedirectManagerInterface::class, RedirectManager::class);
        $this->app->bind(Contracts\PublishManagerInterface::class, PublishManager::class);
        $this->app->bind(Contracts\CacheManagerInterface::class, CacheManager::class);

        $this->app->singleton(RedirectManager::class);
        $this->app->singleton(PublishManager::class);
        $this->app->singleton(CacheManager::class);
    }
}
