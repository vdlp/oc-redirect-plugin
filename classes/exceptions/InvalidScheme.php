<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Exceptions;

use RuntimeException;

/**
 * Class InvalidScheme
 *
 * @package Vdlp\Redirect\Classes\Exceptions
 */
class InvalidScheme extends RuntimeException
{
    /**
     * @param string $scheme
     * @return InvalidScheme
     */
    public static function withScheme($scheme): InvalidScheme
    {
        return new static("Scheme '$scheme' is not a valid scheme. Use 'http' or 'https'.");
    }
}
