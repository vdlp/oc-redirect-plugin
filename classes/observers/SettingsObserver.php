<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Observers;

use Vdlp\Redirect\Classes\Contracts\PublishManagerInterface;

final class SettingsObserver
{
    private $publishManager;

    public function __construct(PublishManagerInterface $publishManager)
    {
        $this->publishManager = $publishManager;
    }

    public function saving(): void
    {
        $this->publishManager->publish();
    }
}
