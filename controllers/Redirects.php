<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Vdlp\Redirect\Classes\CacheManager;
use Vdlp\Redirect\Classes\PublishManager;
use Vdlp\Redirect\Classes\RedirectManager;
use Vdlp\Redirect\Classes\RedirectRule;
use Vdlp\Redirect\Models\Client;
use Vdlp\Redirect\Models\Redirect;
use Vdlp\Redirect\Models\Settings;
use ApplicationException;
use Backend;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ImportExportController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\ReorderController;
use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\Widgets\Form;
use BackendMenu;
use BadMethodCallException;
use Carbon\Carbon;
use Event;
use Exception;
use Flash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Lang;
use Redirect as RedirectFacade;
use Request;
use System\Models\RequestLog;
use SystemException;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Redirects
 *
 * @property array requiredPermissions
 * @package Vdlp\Redirect\Controllers
 * @mixin FormController
 * @mixin ListController
 * @mixin ReorderController
 * @mixin ImportExportController
 */
class Redirects extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ReorderController',
        'Backend.Behaviors.ImportExportController',
    ];

    /** @var string */
    public $formConfig = 'config_form.yaml';

    /** @var string */
    public $listConfig = [
        'list' => 'config_list.yaml',
        'requestLog' => 'request-log/config_list.yaml',
    ];

    /** @var string */
    public $reorderConfig = 'config_reorder.yaml';

    /** @var string */
    public $importExportConfig = 'config_import_export.yaml';

    /** @var PublishManager */
    public $publishManager;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $sideMenuItemCode = in_array($this->action, ['reorder', 'import', 'export'], true)
            ? $this->action
            : 'redirects';

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', $sideMenuItemCode);

        $this->requiredPermissions = ['vdlp.redirect.access_redirects'];

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css', 'Vdlp.Redirect');

        $this->vars['match'] = null;
    }

    /**
     * Index Controller action.
     *
     * @return void
     */
    public function index()//: void
    {
        parent::index();

        if (CacheManager::cachingEnabledButNotSupported()) {
            $this->vars['warningMessage'] = Lang::get('vdlp.redirect::lang.redirect.cache_warning');
        }
    }

    /**
     * Edit Controller action.
     *
     * @param int $recordId The model primary key to update.
     * @param string $context Explicitly define a form context.
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function update($recordId = null, $context = null)
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::findOrFail($recordId);

        if ($redirect->getAttribute('target_type') === Redirect::TARGET_TYPE_STATIC_PAGE
            && !class_exists('\RainLab\Pages\Classes\Page')
        ) {
            Flash::error(Lang::get('vdlp.redirect::lang.flash.static_page_redirect_not_supported'));
            return RedirectFacade::back();
        }

        if (!$redirect->isActiveOnDate(Carbon::now())) {
            $this->vars['warningMessage'] = Lang::get('vdlp.redirect::lang.scheduling.not_active_warning');
        }

        parent::update($recordId, $context);
    }

    // @codingStandardsIgnoreStart

    /**
     * @param string|null $context
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create_onSave($context = null)
    {
        $redirect = parent::create_onSave($context);

        if (post('new')) {
            return Backend::redirect('vdlp/redirect/redirects/create');
        }

        return $redirect;
    }

    /**
     * Delete selected redirects.
     *
     * @return array
     */
    public function index_onDelete(): array
    {
        Redirect::destroy($this->getCheckedIds());
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    /**
     * Enable selected redirects.
     *
     * @return array
     */
    public function index_onEnable(): array
    {
        Redirect::whereIn('id', $this->getCheckedIds())->update(['is_enabled' => 1]);
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    /**
     * Disable selected redirects.
     *
     * @return array
     */
    public function index_onDisable(): array
    {
        Redirect::whereIn('id', $this->getCheckedIds())->update(['is_enabled' => 0]);
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    /**
     * Reset all statistics for selected redirects.
     *
     * @return array
     */
    public function index_onResetStatistics(): array
    {
        $checkedIds = $this->getCheckedIds();

        foreach ($checkedIds as $checkedId) {
            /** @var Redirect $redirect */
            $redirect = Redirect::find($checkedId);
            $redirect->update(['hits' => 0]);
            $redirect->clients()->delete();
        }

        return $this->listRefresh();
    }

    /**
     * Clears redirect cache.
     *
     * @throws BadMethodCallException
     */
    public function index_onClearCache()//: void
    {
        CacheManager::instance()->flush();
        Flash::success(Lang::get('vdlp.redirect::lang.flash.cache_cleared_success'));
    }

    /**
     * Renders actions partial.
     *
     * @return string
     */
    public function index_onLoadActions(): string
    {
        return (string) $this->makePartial('popup_actions', [], true);
    }

    /**
     * Resets all statistics.
     *
     * @return array
     */
    public function index_onResetAllStatistics(): array
    {
        Redirect::query()->update(['hits' => 0]);
        Client::query()->delete();
        Flash::success(Lang::get('vdlp.redirect::lang.flash.statistics_reset_success'));
        return $this->listRefresh();
    }

    /**
     * Enables all redirects.
     *
     * @return array
     */
    public function index_onEnableAllRedirects(): array
    {
        Redirect::query()->update(['is_enabled' => 1]);
        Flash::success(Lang::get('vdlp.redirect::lang.flash.enabled_all_redirects_success'));
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    /**
     * Disables all redirects.
     *
     * @return array
     */
    public function index_onDisableAllRedirects(): array
    {
        Redirect::query()->update(['is_enabled' => 0]);
        Flash::success(Lang::get('vdlp.redirect::lang.flash.disabled_all_redirects_success'));
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    /**
     * Deletes all redirects.
     *
     * @return array
     */
    public function index_onDeleteAllRedirects(): array
    {
        Redirect::query()->delete();
        Flash::success(Lang::get('vdlp.redirect::lang.flash.deleted_all_redirects_success'));
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    // @codingStandardsIgnoreEnd

    /**
     * Renders status code information partial.
     *
     * @return string
     */
    public function onShowStatusCodeInfo(): string
    {
        return (string) $this->makePartial('status_code_info', [], false);
    }

    /**
     * Called after the form fields are defined.
     *
     * @param Form $host
     * @param array $fields
     * @return void
     */
    public function formExtendFields(Form $host, array $fields = [])//: void
    {
        $disableFields = [
            'from_url',
            'to_url',
            'cms_page',
            'target_type',
            'match_type',
        ];

        foreach ($disableFields as $disableField) {
            /** @var FormField $field */
            $field = $host->getField($disableField);
            $field->disabled = $host->model->getAttribute('system');
        }

        if (!Settings::isTestLabEnabled()) {
            $host->removeTab('vdlp.redirect::lang.tab.tab_test_lab');
        }

        if (Request::method() === 'GET') {
            $this->formExtendRefreshFields($host, $fields);
        }
    }

    /**
     * Called when the form is refreshed, giving the opportunity to modify the form fields.
     *
     * @param Form $host The hosting form widget
     * @param array $fields Current form fields
     * @return void
     */
    public function formExtendRefreshFields(Form $host, $fields)//: void
    {
        if ($fields['status_code']->value
            && $fields['status_code']->value[0] === '4'
        ) {
            $host->getField('to_url')->hidden = true;
            $host->getField('static_page')->hidden = true;
            $host->getField('cms_page')->hidden = true;
            $host->getField('to_scheme')->hidden = true;
            return;
        }

        switch ($fields['target_type']->value) {
            case Redirect::TARGET_TYPE_CMS_PAGE:
                $host->getField('to_url')->hidden = true;
                $host->getField('static_page')->hidden = true;
                $host->getField('cms_page')->hidden = false;
                break;
            case Redirect::TARGET_TYPE_STATIC_PAGE:
                $host->getField('to_url')->hidden = true;
                $host->getField('static_page')->hidden = false;
                $host->getField('cms_page')->hidden = true;
                break;
            default:
                $host->getField('to_url')->hidden = false;
                $host->getField('static_page')->hidden = true;
                $host->getField('cms_page')->hidden = true;
                break;
        }
    }

    /**
     * Returns a CSS class name for a list row (<tr class="...">).
     *
     * @param mixed $record The populated model used for the column
     * @return string CSS class name
     */
    public function listInjectRowClass($record): string
    {
        if ($record instanceof Redirect
            && !$record->isActiveOnDate(Carbon::now())
        ) {
            return 'special';
        }

        return '';
    }

    /**
     * Test Input Path.
     *
     * @throws ApplicationException
     * @return array
     */
    public function onTest(): array
    {
        $inputPath = Request::get('inputPath');
        $redirect = new Redirect(Request::get('Redirect'));

        try {
            $rule = RedirectRule::createWithModel($redirect);

            $manager = RedirectManager::createWithRule($rule);

            $testDate = Carbon::createFromFormat('Y-m-d', Request::get('test_date', date('Y-m-d')));
            $manager->setMatchDate($testDate);

            $match = $manager->match($inputPath, Request::get('test_scheme', Request::getScheme()));
        } catch (Exception $e) {
            throw new ApplicationException($e->getMessage());
        }

        return [
            '#testResult' => $this->makePartial('redirect_test_result', [
                'match' => $match,
                'url' => $match ? $manager->getLocation($match) : '',
            ]),
        ];
    }

    /**
     * Triggers Request Log dialog.
     *
     * @return string
     * @throws SystemException
     */
    public function onOpenRequestLog(): string
    {
        $this->makeLists();
        return $this->makePartial('request-log/modal');
    }

    /**
     * Create Redirects from Request Log items.
     *
     * @return array
     */
    public function onCreateRedirectFromRequestLogItems(): array
    {
        $checkedIds = $this->getCheckedIds();
        $redirectsCreated = 0;

        foreach ($checkedIds as $checkedId) {
            /** @var RequestLog $requestLog */
            $requestLog = RequestLog::find($checkedId);

            $url = $this->parseRequestLogItemUrl($requestLog->getAttribute('url'));

            if ($url === '') {
                continue;
            }

            Redirect::create([
                'match_type' => Redirect::TYPE_EXACT,
                'target_type' => Redirect::TARGET_TYPE_PATH_URL,
                'from_url' => $url,
                'to_url' => '/',
                'status_code' => 301,
                'is_enabled' => false,
            ]);

            $redirectsCreated++;
        }

        if ((bool) Request::get('andDelete', false)) {
            RequestLog::destroy($checkedIds);
        }

        if ($redirectsCreated > 0) {
            Event::fire('redirects.changed');

            Flash::success(Lang::get(
                'vdlp.redirect::lang.flash.success_created_redirects',
                [
                    'count' => $redirectsCreated,
                ]
            ));
        }

        return $this->listRefresh();
    }

    /**
     * Check checked ID's from POST request.
     *
     * @return array
     */
    private function getCheckedIds(): array
    {
        if (($checkedIds = post('checked'))
            && is_array($checkedIds)
            && count($checkedIds)
        ) {
            return $checkedIds;
        }

        return [];
    }

    /**
     * @param string $url
     * @return string
     */
    private function parseRequestLogItemUrl($url): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if ($path === false || $path === '/' || $path === '') {
            return '';
        }

        // Using `parse_url($url, PHP_URL_QUERY)` will result in a string of sorted query params (2.0.23):
        // e.g ?a=z&z=a becomes ?z=a&a=z
        // So let's just grab the query part using string functions to make sure whe have the exact query string.
        $questionMarkPosition = strpos($url, '?');

        if ($questionMarkPosition !== false) {
            $path .= substr($url, $questionMarkPosition);
        }

        return $path;
    }

    /** @noinspection PhpUnusedParameterInspection */

    /**
     * Called after the creation or updating form is saved.
     *
     * @param Model
     */
    public function formAfterSave($model)//: void
    {
        Event::fire('redirects.changed');
    }

    /** @noinspection PhpUnusedParameterInspection */

    /**
     * Called after the form model is deleted.
     *
     * @param Model
     */
    public function formAfterDelete($model)//: void
    {
        Event::fire('redirects.changed');
    }
}
