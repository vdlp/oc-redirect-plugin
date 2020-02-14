<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Illuminate\Contracts\Events\Dispatcher;
use Psr\Log\LoggerInterface;
use Vdlp\Redirect\Classes\Contracts\CacheManagerInterface;
use Vdlp\Redirect\Classes\Contracts\PublishManagerInterface;
use Vdlp\Redirect\Models;

final class RedirectObserver
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var PublishManagerInterface
     */
    private $publishManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @param Dispatcher $dispatcher
     * @param LoggerInterface $log
     * @param PublishManagerInterface $publishManager
     * @param CacheManagerInterface $cacheManager
     */
    public function __construct(
        Dispatcher $dispatcher,
        LoggerInterface $log,
        PublishManagerInterface $publishManager,
        CacheManagerInterface $cacheManager
    ) {
        $this->dispatcher = $dispatcher;
        $this->log = $log;
        $this->publishManager = $publishManager;
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param Models\Redirect $model
     * @return void
     */
    public function created(Models\Redirect $model): void
    {
        $this->logChange($model, 'created');
        $this->dispatcher->dispatch('vdlp.redirect.changed', [$model->getKey()]);
    }

    /**
     * @param Models\Redirect $model
     * @return void
     */
    public function updated(Models\Redirect $model): void
    {
        $this->logChange($model, 'updated');
        $this->dispatcher->dispatch('vdlp.redirect.changed', [$model->getKey()]);
    }

    /**
     * @param Models\Redirect $model
     * @return void
     */
    public function saved(Models\Redirect $model): void
    {
        $this->logChange($model, 'saved');
        $this->dispatcher->dispatch('vdlp.redirect.changed', [$model->getKey()]);
    }

    /**
     * @param Models\Redirect $model
     * @return void
     */
    public function deleted(Models\Redirect $model): void
    {
        $this->logChange($model, 'deleted');
        $this->dispatcher->dispatch('vdlp.redirect.changed', [$model->getKey()]);
    }

    /**
     * Handle the type of change.
     *
     * @return void
     */
    private function handle(): void
    {
        if ($this->cacheManager->cachingEnabledAndSupported()) {
            $this->cacheManager->flush();
            $this->log->info('Vdlp.Redirect: Redirect cache has been flushed.');
        }

        $this->publishManager->publish();
        $this->log->info('Vdlp.Redirect: Redirect engine has been updated.');
    }

    /**
     * @param Models\Redirect $model
     * @param string $typeOfChange
     * @return void
     */
    private function logChange(Models\Redirect $model, string $typeOfChange): void
    {
        $this->log->info(sprintf(
            'Vdlp.Redirect: Redirect %d has been %s.',
            $model->getKey(),
            $typeOfChange
        ));
    }
}
