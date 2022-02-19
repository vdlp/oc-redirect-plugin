<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Behaviors;
use Backend\Classes\Controller;
use BackendMenu;

/**
 * @mixin Behaviors\FormController
 * @mixin Behaviors\ListController
 */
final class Categories extends Controller
{
    public $implement = [
        Behaviors\FormController::class,
        Behaviors\ListController::class
    ];

    public $requiredPermissions = ['vdlp.redirect.access_redirects'];
    public string $formConfig = 'config_form.yaml';
    public string $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', 'categories');
    }
}
