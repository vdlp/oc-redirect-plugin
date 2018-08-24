<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Exceptions;

use RuntimeException;

/**
 * Class RulesPathNotReadable
 *
 * @package Vdlp\Redirect\Classes\Exceptions
 */
class RulesPathNotReadable extends RuntimeException
{
    /**
     * @param string $path
     * @return RulesPathNotReadable
     */
    public static function withPath($path): RulesPathNotReadable
    {
        return new static("Rules path $path is not readable.");
    }
}
