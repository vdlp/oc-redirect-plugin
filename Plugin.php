<?php

declare(strict_types=1);

namespace Vdlp\Redirect;

use Vdlp\Redirect\Classes\CacheManager;
use Vdlp\Redirect\Classes\PageHandler;
use Vdlp\Redirect\Classes\PublishManager;
use Vdlp\Redirect\Classes\RedirectMiddleware;
use Vdlp\Redirect\Classes\StaticPageHandler;
use Vdlp\Redirect\Models\Redirect;
use Vdlp\Redirect\Models\Settings;
use Vdlp\Redirect\ReportWidgets\CreateRedirect;
use Vdlp\Redirect\ReportWidgets\TopTenRedirects;
use App;
use Backend;
use Cms\Classes\Page;
use Event;
use Exception;
use System\Classes\PluginBase;
use Illuminate\Contracts\Http\Kernel;

/**
 * Class Plugin
 *
 * @package Vdlp\Redirect
 */
class Plugin extends PluginBase
{
    /**
     * {@inheritdoc}
     */
    public function pluginDetails(): array
    {
        return [
            'name' => 'vdlp.redirect::lang.plugin.name',
            'description' => 'vdlp.redirect::lang.plugin.description',
            'author' => 'Alwin Drenth',
            'icon' => 'icon-link',
            'homepage' => 'https://octobercms.com/plugin/vdlp-redirect',
        ];
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function boot()
    {
        if (App::runningInConsole() || App::runningUnitTests()) {
            return;
        }

        if (!App::runningInBackend()) {
            /** @var Kernel $kernel */
            $kernel = $this->app[Kernel::class];
            $kernel->prependMiddleware(RedirectMiddleware::class);
            return;
        }

        $this->bootBackend();
    }

    /**
     * Boot stuff for Backend
     *
     * @return void
     * @throws Exception
     */
    public function bootBackend()//: void
    {
        Page::extend(function (Page $page) {
            $handler = new PageHandler($page);

            $page->bindEvent('model.beforeUpdate', function () use ($handler) {
                $handler->onBeforeUpdate();
            });

            $page->bindEvent('model.afterDelete', function () use ($handler) {
                $handler->onAfterDelete();
            });
        });

        if (class_exists('\RainLab\Pages\Classes\Page')) {
            \RainLab\Pages\Classes\Page::extend(function (\RainLab\Pages\Classes\Page $page) {
                $handler = new StaticPageHandler($page);

                $page->bindEvent('model.beforeUpdate', function () use ($handler) {
                    $handler->onBeforeUpdate();
                });

                $page->bindEvent('model.afterDelete', function () use ($handler) {
                    $handler->onAfterDelete();
                });
            });
        }

        // When one or more redirects have been changed.
        Event::listen('redirects.changed', function () {
            if (CacheManager::cachingEnabledAndSupported()) {
                CacheManager::instance()->flush();
            }

            PublishManager::instance()->publish();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function registerPermissions(): array
    {
        return [
            'vdlp.redirect.access_redirects' => [
                'label' => 'vdlp.redirect::lang.permission.access_redirects.label',
                'tab' => 'vdlp.redirect::lang.permission.access_redirects.tab',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerNavigation(): array
    {
        $defaultBackendUrl = Backend::url(
            'vdlp/redirect/' . (Settings::isStatisticsEnabled() ? 'statistics' : 'redirects')
        );

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
                'sideMenu' => [
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
                    'import' => [
                        'label' => 'vdlp.redirect::lang.buttons.import',
                        'url' => Backend::url('vdlp/redirect/redirects/import'),
                        'icon' => 'icon-download',
                        'order' => 70,
                        'permissions' => [
                            'vdlp.redirect.access_redirects',
                        ],
                    ],
                    'export' => [
                        'label' => 'vdlp.redirect::lang.buttons.export',
                        'url' => Backend::url('vdlp/redirect/redirects/export'),
                        'icon' => 'icon-upload',
                        'order' => 80,
                        'permissions' => [
                            'vdlp.redirect.access_redirects',
                        ],
                    ],
                    'settings' => [
                        'label' => 'vdlp.redirect::lang.buttons.settings',
                        'url' => Backend::url('system/settings/update/vdlp/redirect/config'),
                        'icon' => 'icon-cogs',
                        'order' => 90,
                        'permissions' => [
                            'vdlp.redirect.access_redirects',
                        ],
                    ],
                ],
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

    /**
     * {@inheritdoc}
     */
    public function registerSettings(): array
    {
        /** @noinspection ClassConstantCanBeUsedInspection */
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
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerReportWidgets(): array
    {
        $reportWidgets[CreateRedirect::class] = [
            'label' => 'vdlp.redirect::lang.buttons.create_redirect',
            'context' => 'dashboard'
        ];

        if (Settings::isStatisticsEnabled()) {
            $reportWidgets[TopTenRedirects::class] = [
                'label' => trans('vdlp.redirect::lang.statistics.top_redirects_this_month', ['top' => 10]),
                'context' => 'dashboard',
            ];
        }

        return $reportWidgets;
    }

    /**
     * {@inheritdoc}
     */
    public function registerListColumnTypes(): array
    {
        return [
            'redirect_switch_color' => function ($value) {
                $format = '<div class="oc-icon-circle" style="color: %s">%s</div>';

                if ((int) $value === 1) {
                    return sprintf($format, '#95b753', e(trans('backend::lang.list.column_switch_true')));
                }

                return sprintf($format, '#cc3300', e(trans('backend::lang.list.column_switch_false')));
            },
            'redirect_match_type' => function ($value) {
                switch ($value) {
                    case Redirect::TYPE_EXACT:
                        return e(trans('vdlp.redirect::lang.redirect.exact'));
                    case Redirect::TYPE_PLACEHOLDERS:
                        return e(trans('vdlp.redirect::lang.redirect.placeholders'));
                    default:
                        return e($value);
                }
            },
            'redirect_status_code' => function ($value) {
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
            'redirect_target_type' => function ($value) {
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
            'redirect_from_url' => function ($value) {
                $maxChars = 40;
                $textLength = strlen($value);
                if ($textLength > $maxChars) {
                    return '<span title="' . e($value) . '">'
                        . e(substr_replace($value, '...', $maxChars / 2, $textLength - $maxChars))
                        . '</span>';
                }
                return e($value);
            },
            'redirect_system' => function ($value) {
                return sprintf(
                    '<span class="%s" title="%s"></span>',
                    $value ? 'oc-icon-magic' : 'oc-icon-user',
                    e(trans('vdlp.redirect::lang.redirect.system_tip'))
                );
            },
        ];
    }
}
