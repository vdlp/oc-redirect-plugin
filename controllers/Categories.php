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
class Categories extends Controller
{
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public $requiredPermissions = ['vdlp.redirect.access_redirects'];

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', 'categories');
    }
}
