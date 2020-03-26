<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Exceptions;

use RuntimeException;

final class RulesPathNotWritable extends RuntimeException
{
    public static function withPath(string $path): self
    {
        return new static("Rules path $path is not writable.");
    }
}
