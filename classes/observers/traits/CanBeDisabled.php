<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Observers\Traits;

trait CanBeDisabled
{
    /**
     * @var bool
     */
    private static $handleChanges = true;

    public static function startHandleChanges(): void
    {
        self::$handleChanges = true;
    }

    public static function stopHandleChanges(): void
    {
        self::$handleChanges = false;
    }

    public static function canHandleChanges(): bool
    {
        return self::$handleChanges;
    }
}
