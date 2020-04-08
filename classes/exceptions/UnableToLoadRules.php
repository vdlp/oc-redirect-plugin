<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Exceptions;

use RuntimeException;
use Throwable;

class UnableToLoadRules extends RuntimeException
{
    public static function withMessage(string $message, ?Throwable $previous = null): self
    {
        return new self('Error while reading rules (' . $message . ')', 0, $previous);
    }
}
