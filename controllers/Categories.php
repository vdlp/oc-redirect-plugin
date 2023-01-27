<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Behaviors;
use Backend\Classes\Controller;
use Backend\Classes\NavigationManager;

/**
 * @mixin Behaviors\FormController
 * @mixin Behaviors\ListController
 */
final class Categories extends Controller
{
    public $implement = [
        Behaviors\FormController::class,
        Behaviors\ListController::class,
    ];

    public $requiredPermissions = ['vdlp.redirect.access_redirects'];
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');

        NavigationManager::instance()->setContext('Vdlp.Redirect', 'redirect', 'categories');
    }
}
