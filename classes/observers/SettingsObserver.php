<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Observers;

use Throwable;
use Vdlp\Redirect\Classes\Contracts\PublishManagerInterface;

final class SettingsObserver
{
    public function __construct(
        private PublishManagerInterface $publishManager
    ) {
    }

    public function saving(): void
    {
        try {
            $this->publishManager->publish();
        } catch (Throwable) {
            // @ignoreException
        }
    }
}
