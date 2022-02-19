<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Vdlp\Redirect\Models\Settings;

final class RedirectManagerSettings
{
    private bool $loggingEnabled;
    private bool $statisticsEnabled;
    private bool $relativePathsEnabled;

    public function __construct(bool $loggingEnabled, bool $statisticsEnabled, bool $relativePathsEnabled)
    {
        $this->loggingEnabled = $loggingEnabled;
        $this->statisticsEnabled = $statisticsEnabled;
        $this->relativePathsEnabled = $relativePathsEnabled;
    }

    public static function createDefault(): RedirectManagerSettings
    {
        return new self(
            Settings::isLoggingEnabled(),
            Settings::isStatisticsEnabled(),
            Settings::isRelativePathsEnabled()
        );
    }

    public function isLoggingEnabled(): bool
    {
        return $this->loggingEnabled;
    }

    public function isStatisticsEnabled(): bool
    {
        return $this->statisticsEnabled;
    }

    public function isRelativePathsEnabled(): bool
    {
        return $this->relativePathsEnabled;
    }
}
