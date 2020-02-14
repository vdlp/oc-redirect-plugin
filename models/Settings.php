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
        return (new static())->get('logging_enabled', true);
    }

    public static function isStatisticsEnabled(): bool
    {
        return (new static())->get('statistics_enabled', true);
    }

    public static function isTestLabEnabled(): bool
    {
        return (new static())->get('test_lab_enabled', true);
    }

    public static function isCachingEnabled(): bool
    {
        return (new static())->get('caching_enabled', false);
    }

    public static function isAutoRedirectCreationEnabled(): bool
    {
        return (new static())->get('auto_redirect_creation_enabled', false);
    }
}
