<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Exceptions;

use RuntimeException;

final class RulesPathNotReadable extends RuntimeException
{
    public static function withPath(string $path): RulesPathNotReadable
    {
        return new static("Rules path $path is not readable.");
    }
}
