<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Classes\Controller;
use Backend\Classes\NavigationManager;
use System\Classes\PluginManager;

final class Extensions extends Controller
{
    /**
     * @var string[]
     */
    private static array $extensions = [
        'Vdlp.RedirectConditions',
        'Vdlp.RedirectConditionsDomain',
        'Vdlp.RedirectConditionsExample',
        'Vdlp.RedirectConditionsHeader',
        'Vdlp.RedirectConditionsUserAgent',
    ];

    public function __construct()
    {
        parent::__construct();

        NavigationManager::instance()->setContext('Vdlp.Redirect', 'redirect', 'extensions');

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');

        $this->pageTitle = 'Redirect Extensions (new)';
    }

    public function index(): void
    {
        $this->vars['extensions'] = [];

        foreach (self::$extensions as $extension) {
            $this->vars['extensions'][$extension] = PluginManager::instance()->hasPlugin($extension)
                && !PluginManager::instance()->isDisabled($extension);
        }
    }
}
