<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Console;

use Illuminate\Console\Command;
use Vdlp\Redirect\Classes\PublishManager;

final class PublishRedirectsCommand extends Command
{
    public function __construct()
    {
        $this->signature = 'vdlp:redirect:publish-redirects';
        $this->description = 'Publish all redirects.';

        parent::__construct();
    }

    public function handle(PublishManager $publishManager): void
    {
        $publishManager->publish();
    }
}
