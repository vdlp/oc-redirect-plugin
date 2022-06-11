<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Contracts;

interface PublishManagerInterface
{
    /**
     * Publish applicable redirects.
     *
     * Returns the number of published redirects.
     */
    public function publish(): int;
}
