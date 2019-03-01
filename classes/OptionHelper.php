<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Cms\Classes\Page;
use Cms\Classes\Theme;
use System\Classes\PluginManager;
use Vdlp\Redirect\Models\Category;
use Vdlp\Redirect\Models\Redirect;

/**
 * Class OptionHelper
 *
 * @package Vdlp\Redirect\Classes
 */
class OptionHelper
{
    /**
     * Returns available target type options based on given status code.
     *
     * @param int $statusCode
     * @return array
     */
    public static function getTargetTypeOptions($statusCode): array
    {
        if ($statusCode === 404 || $statusCode === 410) {
            return [
                Redirect::TARGET_TYPE_NONE => 'vdlp.redirect::lang.redirect.target_type_none',
            ];
        }

        return [
            Redirect::TARGET_TYPE_PATH_URL => 'vdlp.redirect::lang.redirect.target_type_path_or_url',
            Redirect::TARGET_TYPE_CMS_PAGE => 'vdlp.redirect::lang.redirect.target_type_cms_page',
            Redirect::TARGET_TYPE_STATIC_PAGE => 'vdlp.redirect::lang.redirect.target_type_static_page',
        ];
    }

    /**
     * Get all CMS pages as an option array.
     *
     * @return array
     */
    public static function getCmsPageOptions(): array
    {
        return ['' => '-- ' . trans('vdlp.redirect::lang.redirect.none') . ' --' ] + Page::getNameList();
    }

    /**
     * Get all Static Pages as an option array.
     *
     * @return array
     */
    public static function getStaticPageOptions(): array
    {
        $options = ['' => '-- ' . trans('vdlp.redirect::lang.redirect.none') . ' --' ];

        $hasPagesPlugin = PluginManager::instance()->hasPlugin('RainLab.Pages');

        if (!$hasPagesPlugin) {
            return $options;
        }

        /** @noinspection PhpUndefinedClassInspection */
        $pages = \RainLab\Pages\Classes\Page::listInTheme(Theme::getActiveTheme());

        /** @noinspection PhpUndefinedClassInspection */
        /** @var \RainLab\Pages\Classes\Page $page */
        foreach ($pages as $page) {
            /** @noinspection PhpUndefinedFieldInspection */
            if (array_key_exists('title', $page->viewBag)) {
                /** @noinspection PhpUndefinedMethodInspection */
                /** @noinspection PhpUndefinedFieldInspection */
                $options[$page->getBaseFileName()] = $page->viewBag['title'];
            }
        }

        return $options;
    }

    /**
     * Get all categories as an option array.
     *
     * @return array
     */
    public static function getCategoryOptions(): array
    {
        return (array) Category::all(['id', 'name'])->lists('name', 'key');
    }
}
