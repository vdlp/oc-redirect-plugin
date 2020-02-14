<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Illuminate\Contracts\Events\Dispatcher;
use Psr\Log\LoggerInterface;
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

    public function __construct(
        Dispatcher $dispatcher,
        LoggerInterface $log
    ) {
        $this->dispatcher = $dispatcher;
        $this->log = $log;
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
    public function deleted(Models\Redirect $model): void
    {
        $this->logChange($model, 'deleted');
        $this->dispatcher->dispatch('vdlp.redirect.changed', [$model->getKey()]);
    }

    private function logChange(Models\Redirect $model, string $typeOfChange): void
    {
        if (config('vdlp.redirect::log_redirect_changes') === false) {
            return;
        }

        $this->log->info(sprintf(
            'Vdlp.Redirect: Redirect %d has been %s.',
            $model->getKey(),
            $typeOfChange
        ), $model->getDirty());
    }
}
