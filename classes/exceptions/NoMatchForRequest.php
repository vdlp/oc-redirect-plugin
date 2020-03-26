<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Exceptions;

use RuntimeException;

final class NoMatchForRequest extends RuntimeException
{
    public static function withRequestPath(string $requestPath, string $scheme): self
    {
        return new self(sprintf(
            'No match found for request path %s (schema: %s).',
            $requestPath,
            $scheme
        ));
    }
}
