<?php

declare(strict_types=1);

namespace Vdlp\Redirect;

use Illuminate\Cache\TaggedCache;
use Illuminate\Cache\TagSet;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;
use October\Rain\Support\ServiceProvider as ServiceProviderBase;
use Psr\Log\LoggerInterface;
use Vdlp\Redirect\Classes\CacheManager;
use Vdlp\Redirect\Classes\Contracts;
use Vdlp\Redirect\Classes\PublishManager;
use Vdlp\Redirect\Classes\RedirectManager;

final class ServiceProvider extends ServiceProviderBase
{
    public function register(): void
    {
        $this->app->bind(Contracts\RedirectManagerInterface::class, RedirectManager::class);
        $this->app->bind(Contracts\PublishManagerInterface::class, PublishManager::class);
        $this->app->bind(Contracts\CacheManagerInterface::class, static function (Container $container) {
            /** @var Repository $repository */
            $repository = $container->make(Repository::class);

            $taggedCache = new TaggedCache(
                $repository->getStore(),
                new TagSet($repository->getStore(), ['Vdlp.Redirect'])
            );

            return new CacheManager(
                $taggedCache,
                $container->make(LoggerInterface::class)
            );
        });

        $this->app->singleton(RedirectManager::class);
        $this->app->singleton(PublishManager::class);
        $this->app->singleton(CacheManager::class);
    }
}
