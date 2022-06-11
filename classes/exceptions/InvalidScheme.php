<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Exceptions;

use RuntimeException;

final class InvalidScheme extends RuntimeException
{
    public static function withScheme(string $scheme): InvalidScheme
    {
        return new self("Scheme '$scheme' is not a valid scheme. Use 'http' or 'https'.");
    }
}
