<?php

declare(strict_types=1);

namespace Vdlp\Redirect\ServiceProviders;

use Illuminate\Cache\TaggedCache;
use Illuminate\Cache\TagSet;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;
use October\Rain\Support\ServiceProvider;
use Vdlp\Redirect\Classes\CacheManager;
use Vdlp\Redirect\Classes\Contracts;
use Vdlp\Redirect\Classes\PublishManager;
use Vdlp\Redirect\Classes\RedirectManager;

/**
 * Class RedirectManager
 *
 * @package Vdlp\Redirect\ServiceProviders
 */
class Redirect extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(Contracts\RedirectManagerInterface::class, RedirectManager::class);
        $this->app->bind(Contracts\PublishManagerInterface::class, PublishManager::class);

        $this->app->bind(Contracts\CacheManagerInterface::class, static function (Container $container) {
            $repository = $container->make(Repository::class);

            return new CacheManager(new TaggedCache(
                $repository->getStore(),
                new TagSet($repository->getStore(), [
                    'Vdlp.Redirect'
                ])
            ));
        });

        $this->app->singleton(RedirectManager::class);
        $this->app->singleton(PublishManager::class);
        $this->app->singleton(CacheManager::class);
    }
}
