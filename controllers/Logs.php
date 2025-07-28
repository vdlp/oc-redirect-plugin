<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use Backend\Classes\NavigationManager;
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
    public $implement = [
        ListController::class,
    ];

    public $requiredPermissions = ['vdlp.redirect.access_redirects'];
    public $listConfig = 'config_list.yaml';

    public function __construct(
        private Request $request,
        private Translator $translator,
        private LoggerInterface $log,
        private FlashBag $flash
    ) {
        parent::__construct();

        NavigationManager::instance()->setContext('Vdlp.Redirect', 'redirect', 'logs');

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');
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
            foreach ($checkedIds as $recordId) {
                try {
                    /** @var RedirectLog $record */
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
