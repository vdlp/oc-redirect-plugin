<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Exceptions;

final class RulesPathNotReadable extends UnableToLoadRules
{
    public static function withPath(string $path): self
    {
        return new self("Rules path $path is not readable.");
    }
}
