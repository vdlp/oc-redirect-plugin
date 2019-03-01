<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use Exception;
use Flash;
use Lang;
use Vdlp\Redirect\Models\RedirectLog;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Logs
 *
 * @package Vdlp\Redirect\Controllers
 * @mixin ListController
 */
class Logs extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $implement = [
        'Backend.Behaviors.ListController'
    ];

    /** @var string */
    public $listConfig = 'config_list.yaml';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', 'logs');
    }

    // @codingStandardsIgnoreStart

    /**
     * Refresh list.
     *
     * @return array
     */
    public function index_onRefresh(): array
    {
        return $this->listRefresh();
    }

    /**
     * Empty redirect log.
     *
     * @return array
     */
    public function index_onEmptyLog(): array
    {
        RedirectLog::truncate();
        Flash::success(Lang::get('vdlp.redirect::lang.flash.truncate_success'));
        return $this->listRefresh();
    }

    /**
     * Delete 1 or more checked redirect log items.
     *
     * @return array
     */
    public function index_onDelete(): array
    {
        if (($checkedIds = post('checked', []))
            && is_array($checkedIds)
            && count($checkedIds)
        ) {
            foreach ((array) $checkedIds as $recordId) {
                if (!$record = RedirectLog::find($recordId)) {
                    continue;
                }

                try {
                    $record->delete();
                } catch (Exception $e) {
                    // Silence is golden...
                }
            }

            Flash::success(Lang::get('vdlp.redirect::lang.flash.delete_selected_success'));
        }
        else {
            Flash::error(Lang::get('backend::lang.list.delete_selected_empty'));
        }

        return $this->listRefresh();
    }

    // @codingStandardsIgnoreEnd
}
