<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use October\Rain\Flash\FlashBag;
use Psr\Log\LoggerInterface;
use Throwable;
use Vdlp\Redirect\Models\RedirectLog;

/**
 * @mixin ListController
 */
final class Logs extends Controller
{
    /**
     * @var array
     */
    public $implement = [
        ListController::class
    ];

    /**
     * @var string
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array
     */
    public $requiredPermissions = ['vdlp.redirect.access_redirects'];

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var FlashBag
     */
    private $flash;

    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(Request $request, Translator $translator, LoggerInterface $log)
    {
        parent::__construct();

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', 'logs');

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');
        $this->addJs('/plugins/vdlp/redirect/assets/javascript/redirect.js');

        $this->request = $request;
        $this->translator = $translator;
        $this->flash = resolve('flash');
        $this->log = $log;
    }

    public function onRefresh(): array
    {
        return $this->listRefresh();
    }

    public function onEmptyLog(): array
    {
        try {
            RedirectLog::query()->truncate();
            $this->flash->success($this->translator->trans('vdlp.redirect::lang.flash.truncate_success'));
        } catch (Throwable $e) {
            $this->log->warning($e);
        }

        return $this->listRefresh();
    }

    public function onDelete(): array
    {
        if (($checkedIds = $this->request->get('checked', []))
            && is_array($checkedIds)
            && count($checkedIds)
        ) {
            foreach ((array) $checkedIds as $recordId) {
                try {
                    $record = RedirectLog::query()->findOrFail($recordId);
                    $record->delete();
                } catch (Throwable $e) {
                    $this->log->warning($e);
                }
            }

            $this->flash->success($this->translator->trans('vdlp.redirect::lang.flash.delete_selected_success'));
        } else {
            $this->flash->error($this->translator->trans('backend::lang.list.delete_selected_empty'));
        }

        return $this->listRefresh();
    }
}
