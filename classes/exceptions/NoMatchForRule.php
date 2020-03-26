<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Exceptions;

use RuntimeException;
use Vdlp\Redirect\Classes\RedirectRule;

final class NoMatchForRule extends RuntimeException
{
    public static function withRedirectRule(
        RedirectRule $redirectRule,
        string $requestPath,
        ?string $scheme = null
    ): self {
        return new self(sprintf(
            'No match found for rule %d (request path: %s, schema: %s).',
            $redirectRule->getId(),
            $requestPath,
            $scheme ?: '(no scheme)'
        ));
    }
}
