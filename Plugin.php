<?php

declare(strict_types=1);

namespace Vdlp\Redirect;

use Backend\Facades\Backend;
use Event;
use Exception;
use Illuminate\Contracts\Translation\Translator;
use System\Classes\PluginBase;
use Throwable;
use Validator;
use Vdlp\Redirect\Classes\Contracts\PublishManagerInterface;
use Vdlp\Redirect\Classes\Observers;
use Vdlp\Redirect\Classes\RedirectMiddleware;
use Vdlp\Redirect\Console\PublishRedirectsCommand;
use Vdlp\Redirect\Models\Redirect;
use Vdlp\Redirect\Models\Settings;

final class Plugin extends PluginBase
{
    public function pluginDetails(): array
    {
        return [
            'name' => 'vdlp.redirect::lang.plugin.name',
            'description' => 'vdlp.redirect::lang.plugin.description',
            'author' => 'Van der Let & Partners',
            'icon' => 'icon-link',
            'homepage' => 'https://octobercms.com/plugin/vdlp-redirect',
        ];
    }

    /**
     * @throws Exception
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole() || $this->app->runningUnitTests()) {
            return;
        }

        $this->registerCustomValidators();
        $this->registerObservers();

        if (!$this->app->runningInBackend()) {
            $this->app['Illuminate\Contracts\Http\Kernel']
                ->prependMiddleware(RedirectMiddleware::class);
        }
    }

    public function register(): void
    {
        $this->app->register(ServiceProvider::class);

        $this->registerConsoleCommands();
        $this->registerEventListeners();
    }

    public function registerPermissions(): array
    {
        return [
            'vdlp.redirect.access_redirects' => [
                'label' => 'vdlp.redirect::lang.permission.access_redirects.label',
                'tab' => 'vdlp.redirect::lang.permission.access_redirects.tab',
            ],
        ];
    }

    public function registerNavigation(): array
    {
        $defaultBackendUrl = Backend::url(
            'vdlp/redirect/' . (Settings::isStatisticsEnabled() ? 'statistics' : 'redirects')
        );

        $sideMenu = [
            'redirects' => [
                'icon' => 'icon-list',
                'label' => 'vdlp.redirect::lang.navigation.menu_label',
                'url' => Backend::url('vdlp/redirect/redirects'),
                'order' => 20,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ],
            'categories' => [
                'label' => 'vdlp.redirect::lang.buttons.categories',
                'url' => Backend::url('vdlp/redirect/categories'),
                'icon' => 'icon-tag',
                'order' => 60,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ],
        ];

        if ((bool) config('vdlp.redirect::navigation.show_import', true) === true) {
            $sideMenu['import'] = [
                'label' => 'vdlp.redirect::lang.buttons.import',
                'url' => Backend::url('vdlp/redirect/redirects/import'),
                'icon' => 'icon-download',
                'order' => 70,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ];
        }

        if ((bool) config('vdlp.redirect::navigation.show_export', true) === true) {
            $sideMenu['export'] = [
                'label' => 'vdlp.redirect::lang.buttons.export',
                'url' => Backend::url('vdlp/redirect/redirects/export'),
                'icon' => 'icon-upload',
                'order' => 80,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ];
        }

        if ((bool) config('vdlp.redirect::navigation.show_settings', true) === true) {
            $sideMenu['settings'] = [
                'label' => 'vdlp.redirect::lang.buttons.settings',
                'url' => Backend::url('system/settings/update/vdlp/redirect/config'),
                'icon' => 'icon-cogs',
                'order' => 90,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ];
        }

        if ((bool) config('vdlp.redirect::navigation.show_extensions', true) === true) {
            $sideMenu['extensions'] = [
                'label' => 'vdlp.redirect::lang.buttons.extensions',
                'url' => Backend::url('vdlp/redirect/extensions'),
                'icon' => 'icon-cubes',
                'order' => 100,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ];
        }

        $navigation = [
            'redirect' => [
                'label' => 'vdlp.redirect::lang.navigation.menu_label',
                'iconSvg' => '/plugins/vdlp/redirect/assets/images/icon.svg',
                'icon' => 'icon-link',
                'url' => $defaultBackendUrl,
                'order' => 201,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
                'sideMenu' => $sideMenu,
            ],
        ];

        if (Settings::isStatisticsEnabled()) {
            $navigation['redirect']['sideMenu']['statistics'] = [
                'icon' => 'icon-bar-chart',
                'label' => 'vdlp.redirect::lang.title.statistics',
                'url' => Backend::url('vdlp/redirect/statistics'),
                'order' => 10,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ];
        }

        if (Settings::isTestLabEnabled()) {
            $navigation['redirect']['sideMenu']['test_lab'] = [
                'icon' => 'icon-flask',
                'label' => 'vdlp.redirect::lang.title.test_lab',
                'url' => Backend::url('vdlp/redirect/testlab'),
                'order' => 30,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ];
        }

        if (Settings::isLoggingEnabled()) {
            $navigation['redirect']['sideMenu']['logs'] = [
                'label' => 'vdlp.redirect::lang.buttons.logs',
                'url' => Backend::url('vdlp/redirect/logs'),
                'icon' => 'icon-file-text-o',
                'visible' => false,
                'order' => 50,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ];
        }

        return $navigation;
    }

    public function registerSettings(): array
    {
        return [
            'config' => [
                'label' => 'vdlp.redirect::lang.settings.menu_label',
                'description' => 'vdlp.redirect::lang.settings.menu_description',
                'icon' => 'icon-link',
                'class' => Settings::class,
                'order' => 600,
                'permissions' => [
                    'vdlp.redirect.access_redirects',
                ],
            ],
        ];
    }

    public function registerReportWidgets(): array
    {
        /** @var Translator $translator */
        $translator = resolve(Translator::class);

        $reportWidgets[ReportWidgets\CreateRedirect::class] = [
            'label' => 'vdlp.redirect::lang.buttons.create_redirect',
            'context' => 'dashboard',
        ];

        if (Settings::isStatisticsEnabled()) {
            $reportWidgets[ReportWidgets\TopTenRedirects::class] = [
                'label' => e($translator->trans(
                    'vdlp.redirect::lang.statistics.top_redirects_this_month',
                    [
                        'top' => 10,
                    ]
                )),
                'context' => 'dashboard',
            ];
        }

        return $reportWidgets;
    }

    public function registerListColumnTypes(): array
    {
        return [
            'redirect_switch_color' => static function ($value): string {
                $format = '<div class="oc-icon-circle" style="color: %s">%s</div>';

                if ((int) $value === 1) {
                    return sprintf($format, '#95b753', e(trans('backend::lang.list.column_switch_true')));
                }

                return sprintf($format, '#cc3300', e(trans('backend::lang.list.column_switch_false')));
            },
            'redirect_match_type' => static function ($value): string {
                switch ($value) {
                    case Redirect::TYPE_EXACT:
                        return e(trans('vdlp.redirect::lang.redirect.exact'));
                    case Redirect::TYPE_PLACEHOLDERS:
                        return e(trans('vdlp.redirect::lang.redirect.placeholders'));
                    case Redirect::TYPE_REGEX:
                        return e(trans('vdlp.redirect::lang.redirect.regex'));
                    default:
                        return e($value);
                }
            },
            'redirect_status_code' => static function ($value): string {
                switch ($value) {
                    case 301:
                        return e(trans('vdlp.redirect::lang.redirect.permanent'));
                    case 302:
                        return e(trans('vdlp.redirect::lang.redirect.temporary'));
                    case 303:
                        return e(trans('vdlp.redirect::lang.redirect.see_other'));
                    case 404:
                        return e(trans('vdlp.redirect::lang.redirect.not_found'));
                    case 410:
                        return e(trans('vdlp.redirect::lang.redirect.gone'));
                    default:
                        return e($value);
                }
            },
            'redirect_target_type' => static function ($value): string {
                switch ($value) {
                    case Redirect::TARGET_TYPE_PATH_URL:
                        return e(trans('vdlp.redirect::lang.redirect.target_type_path_or_url'));
                    case Redirect::TARGET_TYPE_CMS_PAGE:
                        return e(trans('vdlp.redirect::lang.redirect.target_type_cms_page'));
                    case Redirect::TARGET_TYPE_STATIC_PAGE:
                        return e(trans('vdlp.redirect::lang.redirect.target_type_static_page'));
                    default:
                        return e($value);
                }
            },
            'redirect_from_url' => static function ($value): string {
                $maxChars = 40;
                $textLength = strlen($value);

                if ($textLength > $maxChars) {
                    return '<span title="' . e($value) . '">'
                        . e(substr_replace($value, '...', $maxChars / 2, $textLength - $maxChars))
                        . '</span>';
                }

                return e($value);
            },
            'redirect_system' => static function ($value): string {
                return sprintf(
                    '<span class="%s" title="%s"></span>',
                    $value ? 'oc-icon-magic' : 'oc-icon-user',
                    e(trans('vdlp.redirect::lang.redirect.system_tip'))
                );
            },
        ];
    }

    public function registerSchedule($schedule): void
    {
        $schedule->command('vdlp:redirect:publish-redirects')
            ->dailyAt(config('vdlp.redirect::cron.publish_redirects', '00:00'));
    }

    private function registerConsoleCommands(): void
    {
        $this->registerConsoleCommand('vdlp.redirect.publish-redirects', PublishRedirectsCommand::class);
    }

    private function registerCustomValidators(): void
    {
        Validator::extend('is_regex', static function ($attribute, $value): bool {
            try {
                preg_match($value, '');
            } catch (Throwable) {
                return false;
            }

            return true;
        });
    }

    private function registerObservers(): void
    {
        Redirect::observe(Observers\RedirectObserver::class);
        Settings::observe(Observers\SettingsObserver::class);
    }

    private function registerEventListeners(): void
    {
        /*
         * Extensibility:
         *
         * Allows third-party plugin develop to notify when a URL has changed.
         * E.g. An editor changes the slug of a blog item.
         *
         * `Event::fire('vdlp.redirect.toUrlChanged', [$oldSlug, $newSlug])`
         *
         * Only 'exact' redirects will be supported.
         */
        Event::listen('vdlp.redirect.toUrlChanged', static function (string $oldUrl, string $newUrl): void {
            Redirect::query()
                ->where('match_type', '=', Redirect::TYPE_EXACT)
                ->where('target_type', '=', Redirect::TARGET_TYPE_PATH_URL)
                ->where('to_url', '=', $oldUrl)
                ->where('is_enabled', '=', true)
                ->update([
                    'to_url' => $newUrl,
                    'system' => true,
                ]);
        });

        /*
         * Extensibility:
         *
         * When one or more redirects have been changed.
         */
        Event::listen('vdlp.redirect.changed', static function (array $redirectIds): void {
            try {
                /** @var PublishManagerInterface $publishManager */
                $publishManager = resolve(PublishManagerInterface::class);
                $publishManager->publish();
            } catch (Throwable) {
                // @ignoreException
            }
        });

        /*
         * Cache Management:
         *
         * Re-publish all redirect if cache has been cleared.
         */
        Event::listen('cache:cleared', static function (): void {
            try {
                /** @var PublishManagerInterface $publishManager */
                $publishManager = resolve(PublishManagerInterface::class);
                $publishManager->publish();
            } catch (Throwable) {
                // @ignoreException
            }
        });
    }
}
