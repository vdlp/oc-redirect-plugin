<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Vdlp\Redirect\Models\Settings;

final class RedirectManagerSettings
{
    /**
     * Whether the manager should log redirects.
     *
     * @var bool
     */
    private $loggingEnabled;

    /**
     * Whether the manager should gather statistics.
     *
     * @var bool
     */
    private $statisticsEnabled;

    /**
     * @var bool
     */
    private $relativePathsEnabled;

    /**
     * @param bool $loggingEnabled
     * @param bool $statisticsEnabled
     */
    public function __construct(bool $loggingEnabled, bool $statisticsEnabled, bool $relativePathsEnabled)
    {
        $this->loggingEnabled = $loggingEnabled;
        $this->statisticsEnabled = $statisticsEnabled;
        $this->relativePathsEnabled = $relativePathsEnabled;
    }

    /**
     * @return RedirectManagerSettings
     */
    public static function createDefault(): RedirectManagerSettings
    {
        return new self(
            Settings::isLoggingEnabled(),
            Settings::isStatisticsEnabled(),
            Settings::isRelativePathsEnabled()
        );
    }

    /**
     * @return bool
     */
    public function isLoggingEnabled(): bool
    {
        return $this->loggingEnabled;
    }

    /**
     * @return bool
     */
    public function isStatisticsEnabled(): bool
    {
        return $this->statisticsEnabled;
    }

    /**
     * @return bool
     */
    public function isRelativePathsEnabled(): bool
    {
        return $this->relativePathsEnabled;
    }
}
