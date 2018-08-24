<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use October\Rain\Database\Model;
use System\Behaviors\SettingsModel;

/**
 * Class Settings
 *
 * @property array implement
 * @package Vdlp\Redirect\Models
 * @mixin SettingsModel
 */
class Settings extends Model
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
     * {@inheritdoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->implement = ['System.Behaviors.SettingsModel'];

        parent::__construct($attributes);
    }

    /**
     * Whether logging is enabled.
     *
     * @return bool
     */
    public static function isLoggingEnabled(): bool
    {
        // Please properly document your API/code OctoberCMS!
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (bool) self::get('logging_enabled', true);
    }

    /**
     * Whether gathering of statistics are enabled.
     *
     * @return bool
     */
    public static function isStatisticsEnabled(): bool
    {
        // Please properly document your API/code OctoberCMS!
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (bool) self::get('statistics_enabled', true);
    }

    /**
     * Whether the Test Lab functionality is enabled.
     *
     * @return bool
     */
    public static function isTestLabEnabled(): bool
    {
        // Please properly document your API/code OctoberCMS!
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (bool) self::get('test_lab_enabled', true);
    }

    /**
     * Whether redirect caching is enabled.
     *
     * @return bool
     */
    public static function isCachingEnabled(): bool
    {
        // Please properly document your API/code OctoberCMS!
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (bool) self::get('caching_enabled', false);
    }
}
