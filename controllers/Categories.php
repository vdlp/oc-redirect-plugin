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
    /**
     * @var array
     */
    public $implement = [
        Behaviors\FormController::class,
        Behaviors\ListController::class
    ];

    /**
     * @var string
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array
     */
    public $requiredPermissions = ['vdlp.redirect.access_redirects'];

    public function __construct()
    {
        parent::__construct();

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');
        $this->addJs('/plugins/vdlp/redirect/assets/javascript/redirect.js');

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', 'categories');
    }
}
