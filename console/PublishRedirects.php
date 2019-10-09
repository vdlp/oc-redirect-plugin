<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Console;

use Illuminate\Console\Command;
use Vdlp\Redirect\Classes\PublishManager;

/**
 * Class PublishRedirects
 *
 * @package Vdlp\Redirect\Console
 */
class PublishRedirects extends Command
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $this->name = 'vdlp:redirect:publish-redirects';
        $this->description = 'Publish all redirects.';

        parent::__construct();
    }

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        PublishManager::instance()->publish();
    }
}
