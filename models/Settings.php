<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use October\Rain\Database\Model;
use System\Behaviors\SettingsModel;

/**
 * @property array $implement
 * @mixin SettingsModel
 */
final class Settings extends Model
{
    /**
     * The settings code which to save the settings under.
     *
     * @var string
     */
    public $settingsCode = 'vdlp_redirect_settings';

    /**
     * Form fields definition file.
     *
     * @var string
     */
    public $settingsFields = 'fields.yaml';

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->implement = [SettingsModel::class];

        parent::__construct($attributes);
    }

    public static function isLoggingEnabled(): bool
    {
        return (bool) (new self)->get('logging_enabled', true);
    }

    public static function isStatisticsEnabled(): bool
    {
        return (bool) (new self)->get('statistics_enabled', true);
    }

    public static function isTestLabEnabled(): bool
    {
        return (bool) (new self)->get('test_lab_enabled', true);
    }

    public static function isCachingEnabled(): bool
    {
        return (bool) (new self)->get('caching_enabled', false);
    }

    public static function isRelativePathsEnabled(): bool
    {
        return (bool) (new self)->get('relative_paths_enabled', false);
    }
}
