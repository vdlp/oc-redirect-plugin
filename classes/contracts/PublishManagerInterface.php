<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Contracts;

interface PublishManagerInterface
{
    /**
     * Publish applicable redirects.
     *
     * @return int Number of published redirects
     */
    public function publish(): int;
}
