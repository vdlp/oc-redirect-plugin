<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Util;

final class Str
{
    public static function removeTrailingSlash(string $url): string
    {
        if (str_contains($url, '?')) {
            [$part1, $part2] = explode('?', $url, 2);

            return implode('?', [rtrim($part1, '/'), $part2]);
        }

        return rtrim($url, '/');
    }
}
