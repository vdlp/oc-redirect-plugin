<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use ApplicationException;
use Backend;
use Backend\Behaviors;
use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\Widgets\Form;
use BackendMenu;
use Carbon\Carbon;
use Cms\Classes\CmsException;
use Event;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Lang;
use October\Rain\Flash\FlashBag;
use Request;
use System\Models\RequestLog;
use SystemException;
use Vdlp\Redirect\Classes\CacheManager;
use Vdlp\Redirect\Classes\Contracts\CacheManagerInterface;
use Vdlp\Redirect\Classes\RedirectManager;
use Vdlp\Redirect\Classes\RedirectRule;
use Vdlp\Redirect\Classes\StatisticsHelper;
use Vdlp\Redirect\Models;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Redirects
 *
 * @property array requiredPermissions
 * @package Vdlp\Redirect\Controllers
 * @mixin Behaviors\FormController
 * @mixin Behaviors\ListController
 * @mixin Behaviors\ReorderController
 * @mixin Behaviors\ImportExportController
 */
class Redirects extends Controller
{
    /**
     * {@inheritDoc}
     */
    public $implement = [
        Behaviors\FormController::class,
        Behaviors\ListController::class,
        Behaviors\ReorderController::class,
        Behaviors\ImportExportController::class
    ];

    /**
     * @var string
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string
     */
    public $listConfig = [
        'list' => 'config_list.yaml',
        'requestLog' => 'request-log/config_list.yaml',
    ];

    /**
     * @var string
     */
    public $reorderConfig = 'config_reorder.yaml';

    /**
     * @var string
     */
    public $importExportConfig = 'config_import_export.yaml';

    /**
     * {@inheritDoc}
     */
    public $requiredPermissions = ['vdlp.redirect.access_redirects'];

    /**
     * @var FlashBag
     */
    private $flash;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * {@inheritDoc}
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
        $this->vars['statisticsHelper'] = new StatisticsHelper();

        $this->flash = resolve('flash');
        $this->dispatcher = resolve(Dispatcher::class);
    }

    /**
     * Index Controller action.
     *
     * @return void
     */
    public function index(): void
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
        $this->bodyClass = 'compact-container';

        /** @var Models\Redirect $redirect */
        $redirect = Models\Redirect::findOrFail($recordId);

        if ($redirect->getAttribute('target_type') === Models\Redirect::TARGET_TYPE_STATIC_PAGE
            && !class_exists('\RainLab\Pages\Classes\Page')
        ) {
            $this->flash->error(Lang::get('vdlp.redirect::lang.flash.static_page_redirect_not_supported'));
            return redirect()->back();
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
        $redirectIds = $this->getCheckedIds();

        Models\Redirect::destroy($redirectIds);

        $this->dispatcher->dispatch('vdlp.redirects.changed', ['redirectIds' => $redirectIds]);

        return $this->listRefresh();
    }

    /**
     * Enable selected redirects.
     *
     * @return array
     */
    public function index_onEnable(): array
    {
        $redirectIds = $this->getCheckedIds();

        Models\Redirect::query()
            ->whereIn('id', $redirectIds)
            ->update(['is_enabled' => 1]);

        $this->dispatcher->dispatch('vdlp.redirects.changed', ['redirectIds' => $redirectIds]);

        return $this->listRefresh();
    }

    /**
     * Disable selected redirects.
     *
     * @return array
     */
    public function index_onDisable(): array
    {
        $redirectIds = $this->getCheckedIds();

        Models\Redirect::query()
            ->whereIn('id', $redirectIds)
            ->update(['is_enabled' => 0]);

        $this->dispatcher->dispatch('vdlp.redirects.changed', ['redirectIds' => $redirectIds]);

        return $this->listRefresh();
    }

    /**
     * Reset all statistics for selected redirects.
     *
     * @return array
     */
    public function index_onResetStatistics(): array
    {
        $redirectIds = $this->getCheckedIds();

        Models\Redirect::query()
            ->whereIn('id', $redirectIds)
            ->update(['hits' => 0]);

        // When DB does not support cascading delete.
        Models\Client::query()
            ->whereIn('redirect_id', $redirectIds)
            ->delete();

        $this->dispatcher->dispatch('vdlp.redirects.changed', ['redirectIds' => $redirectIds]);

        return $this->listRefresh();
    }

    /**
     * Clears redirect cache.
     *
     * @return void
     */
    public function index_onClearCache()//: void
    {
        /** @var CacheManagerInterface $cacheManager */
        $cacheManager = resolve(CacheManagerInterface::class);
        $cacheManager->flush();

        $this->flash->success(Lang::get('vdlp.redirect::lang.flash.cache_cleared_success'));
    }

    /**
     * Renders actions partial.
     *
     * @return string
     * @throws SystemException
     */
    public function index_onLoadActions(): string
    {
        return (string) $this->makePartial('popup_actions');
    }

    /**
     * Resets all statistics.
     *
     * @return array
     */
    public function index_onResetAllStatistics(): array
    {
        $redirectIds = $this->getAllRedirectIds();

        Models\Redirect::query()->update(['hits' => 0]);
        Models\Client::query()->delete();

        $this->flash->success(Lang::get('vdlp.redirect::lang.flash.statistics_reset_success'));

        $this->dispatcher->dispatch('vdlp.redirects.changed', ['redirectIds' => $redirectIds]);

        return $this->listRefresh();
    }

    /**
     * Enables all redirects.
     *
     * @return array
     */
    public function index_onEnableAllRedirects(): array
    {
        return $this->toggleRedirects(true);
    }

    /**
     * Disables all redirects.
     *
     * @return array
     */
    public function index_onDisableAllRedirects(): array
    {
        return $this->toggleRedirects(false);
    }

    /**
     * @param bool $enabled
     * @return array
     */
    private function toggleRedirects(bool $enabled): array
    {
        $redirectIds = $this->getAllRedirectIds();

        Models\Redirect::query()
            ->update(['is_enabled' => $enabled]);

        $this->flash->success(Lang::get('vdlp.redirect::lang.flash.disabled_all_redirects_success'));

        $this->dispatcher->dispatch('vdlp.redirects.changed', $redirectIds);

        return $this->listRefresh();
    }

    /**
     * Deletes all redirects.
     *
     * @return array
     */
    public function index_onDeleteAllRedirects(): array
    {
        $redirectIds = $this->getAllRedirectIds();

        Models\Redirect::query()
            ->delete();

        $this->flash->success(Lang::get('vdlp.redirect::lang.flash.deleted_all_redirects_success'));

        $this->dispatcher->dispatch('vdlp.redirects.changed', ['redirectIds' => $redirectIds]);

        return $this->listRefresh();
    }

    // @codingStandardsIgnoreEnd

    /**
     * Renders status code information partial.
     *
     * @return string
     * @throws SystemException
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
    public function formExtendFields(Form $host, array $fields = []): void
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

        if (!Models\Settings::isTestLabEnabled()) {
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
    public function formExtendRefreshFields(Form $host, $fields): void
    {
        if ($fields['status_code']->value
            && strpos((string) $fields['status_code']->value, '4') === 0
        ) {
            $host->getField('to_url')->hidden = true;
            $host->getField('static_page')->hidden = true;
            $host->getField('cms_page')->hidden = true;
            $host->getField('to_scheme')->hidden = true;
            return;
        }

        switch ($fields['target_type']->value) {
            case Models\Redirect::TARGET_TYPE_CMS_PAGE:
                $host->getField('to_url')->hidden = true;
                $host->getField('static_page')->hidden = true;
                $host->getField('cms_page')->hidden = false;
                break;
            case Models\Redirect::TARGET_TYPE_STATIC_PAGE:
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
        if ($record instanceof Models\Redirect
            && !$record->isActiveOnDate(Carbon::now())
        ) {
            return 'special';
        }

        return '';
    }

    /**
     * Test Input Path.
     *
     * @return array
     * @throws ApplicationException
     * @throws SystemException
     * @throws CmsException
     */
    public function onTest(): array
    {
        $inputPath = Request::get('inputPath');
        $redirect = new Models\Redirect(Request::get('Redirect'));

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
     * @throws ModelNotFoundException
     */
    public function onCreateRedirectFromRequestLogItems(): array
    {
        $checkedIds = $this->getCheckedIds();
        $redirectsCreated = 0;

        foreach ($checkedIds as $checkedId) {
            /** @var RequestLog $requestLog */
            $requestLog = RequestLog::query()->findOrFail($checkedId);

            $url = $this->parseRequestLogItemUrl($requestLog->getAttribute('url'));

            if ($url === '') {
                continue;
            }

            Models\Redirect::create([
                'match_type' => Models\Redirect::TYPE_EXACT,
                'target_type' => Models\Redirect::TARGET_TYPE_PATH_URL,
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
            Event::fire('vdlp.redirect.changed');  // TODO: This event will be removed soon.

            $this->flash->success(Lang::get(
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
     * @return array
     */
    private function getAllRedirectIds(): array
    {
        return Models\Redirect::query()
            ->get()
            ->pluck('id')
            ->toArray();
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
}
