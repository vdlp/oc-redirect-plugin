<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Behaviors;
use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\Facades\Backend;
use Backend\Facades\BackendMenu;
use Backend\Widgets\Form;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use October\Rain\Database\Model;
use October\Rain\Exception\ApplicationException;
use October\Rain\Exception\SystemException;
use October\Rain\Flash\FlashBag;
use System\Models\RequestLog;
use Throwable;
use Vdlp\Redirect\Classes\Contracts\CacheManagerInterface;
use Vdlp\Redirect\Classes\Exceptions\InvalidScheme;
use Vdlp\Redirect\Classes\Exceptions\NoMatchForRequest;
use Vdlp\Redirect\Classes\Exceptions\UnableToLoadRules;
use Vdlp\Redirect\Classes\Observers\RedirectObserver;
use Vdlp\Redirect\Classes\RedirectManager;
use Vdlp\Redirect\Classes\RedirectRule;
use Vdlp\Redirect\Classes\StatisticsHelper;
use Vdlp\Redirect\Models;

/**
 * @mixin Behaviors\FormController
 * @mixin Behaviors\ListController
 * @mixin Behaviors\ReorderController
 * @mixin Behaviors\ImportExportController
 * @mixin Behaviors\RelationController
 */
final class Redirects extends Controller
{
    /**
     * {@inheritDoc}
     */
    public $implement = [
        Behaviors\FormController::class,
        Behaviors\ListController::class,
        Behaviors\ReorderController::class,
        Behaviors\ImportExportController::class,
        Behaviors\RelationController::class,
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
     * @var string
     */
    public $relationConfig = 'config_relation.yaml';

    /**
     * {@inheritDoc}
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
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var FlashBag
     */
    private $flash;

    public function __construct(
        Request $request,
        Translator $translator,
        Dispatcher $dispatcher,
        CacheManagerInterface $cacheManager
    ) {
        parent::__construct();

        $sideMenuItemCode = in_array($this->action, ['reorder', 'import', 'export'], true)
            ? $this->action
            : 'redirects';

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', $sideMenuItemCode);

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');
        $this->addJs('/plugins/vdlp/redirect/assets/javascript/redirect.js');

        $this->vars['match'] = null;
        $this->vars['statisticsHelper'] = new StatisticsHelper();

        $this->request = $request;
        $this->translator = $translator;
        $this->dispatcher = $dispatcher;
        $this->cacheManager = $cacheManager;
        $this->flash = resolve('flash');
    }

    public function index(): void
    {
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        parent::index();

        if ($this->cacheManager->cachingEnabledButNotSupported()) {
            $this->vars['warningMessage'] = $this->translator->trans('vdlp.redirect::lang.redirect.cache_warning');
        }
    }

    /**
     * @throws ModelNotFoundException
     * @noinspection PhpStrictTypeCheckingInspection
     */
    public function update($recordId = null, $context = null)
    {
        $this->bodyClass = 'compact-container';

        /** @var Models\Redirect $redirect */
        $redirect = Models\Redirect::query()->findOrFail($recordId);

        /** @noinspection ClassConstantCanBeUsedInspection */
        if ($redirect->getAttribute('target_type') === Models\Redirect::TARGET_TYPE_STATIC_PAGE
            && !class_exists('\RainLab\Pages\Classes\Page')
        ) {
            $this->flash->error(
                $this->translator->trans('vdlp.redirect::lang.flash.static_page_redirect_not_supported')
            );

            return redirect()->back();
        }

        if (!$redirect->isActiveOnDate(Carbon::now())) {
            $this->vars['warningMessage'] = $this->translator->trans(
                'vdlp.redirect::lang.scheduling.not_active_warning'
            );
        }

        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        parent::update($recordId, $context);
    }

    // @codingStandardsIgnoreStart

    public function getCacheManager(): CacheManagerInterface
    {
        return $this->cacheManager;
    }

    public function create_onSave(?string $context = null): RedirectResponse
    {
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $redirect = parent::create_onSave($context);

        if ($this->request->has('new')) {
            return Backend::redirect('vdlp/redirect/redirects/create');
        }

        return $redirect;
    }

    public function index_onDelete(): array
    {
        $redirectIds = $this->getCheckedIds();

        Models\Redirect::destroy($redirectIds);

        $this->dispatcher->dispatch('vdlp.redirect.changed', [
            'redirectIds' => Arr::wrap($redirectIds)
        ]);

        return $this->listRefresh();
    }

    public function index_onEnable(): array
    {
        $redirectIds = $this->getCheckedIds();

        Models\Redirect::query()
            ->whereIn('id', $redirectIds)
            ->update(['is_enabled' => 1]);

        $this->dispatcher->dispatch('vdlp.redirect.changed', [
            'redirectIds' => Arr::wrap($redirectIds)
        ]);

        return $this->listRefresh();
    }

    public function index_onDisable(): array
    {
        $redirectIds = $this->getCheckedIds();

        Models\Redirect::query()
            ->whereIn('id', $redirectIds)
            ->update(['is_enabled' => 0]);

        $this->dispatcher->dispatch('vdlp.redirect.changed', [
            'redirectIds' => Arr::wrap($redirectIds)
        ]);

        return $this->listRefresh();
    }

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

        $this->dispatcher->dispatch('vdlp.redirect.changed', [
            'redirectIds' => Arr::wrap($redirectIds)
        ]);

        return $this->listRefresh();
    }

    public function index_onClearCache(): void
    {
        /** @var CacheManagerInterface $cacheManager */
        $cacheManager = resolve(CacheManagerInterface::class);
        $cacheManager->flush();

        $this->flash->success($this->translator->trans('vdlp.redirect::lang.flash.cache_cleared_success'));
    }

    /**
     * @throws SystemException
     */
    public function index_onLoadActions(): string
    {
        return (string) $this->makePartial('popup_actions');
    }

    public function index_onResetAllStatistics(): array
    {
        $redirectIds = $this->getAllRedirectIds();

        RedirectObserver::stopHandleChanges();

        Models\Redirect::query()->update(['hits' => 0]);
        Models\Client::query()->delete();

        $this->flash->success($this->translator->trans('vdlp.redirect::lang.flash.statistics_reset_success'));

        $this->dispatcher->dispatch('vdlp.redirect.changed', [
            'redirectIds' => Arr::wrap($redirectIds)
        ]);

        return $this->listRefresh();
    }

    public function index_onEnableAllRedirects(): array
    {
        return $this->toggleRedirects(true);
    }

    public function index_onDisableAllRedirects(): array
    {
        return $this->toggleRedirects(false);
    }

    private function toggleRedirects(bool $enabled): array
    {
        $redirectIds = $this->getAllRedirectIds();

        Models\Redirect::query()
            ->update(['is_enabled' => $enabled]);

        $this->flash->success($this->translator->trans('vdlp.redirect::lang.flash.disabled_all_redirects_success'));

        $this->dispatcher->dispatch('vdlp.redirect.changed', [
            'redirectIds' => Arr::wrap($redirectIds)
        ]);

        return $this->listRefresh();
    }

    public function index_onDeleteAllRedirects(): array
    {
        $redirectIds = $this->getAllRedirectIds();

        Models\Redirect::query()
            ->delete();

        $this->flash->success($this->translator->trans('vdlp.redirect::lang.flash.deleted_all_redirects_success'));

        $this->dispatcher->dispatch('vdlp.redirect.changed', [
            'redirectIds' => Arr::wrap($redirectIds)
        ]);

        return $this->listRefresh();
    }

    // @codingStandardsIgnoreEnd

    /**
     * @throws SystemException
     */
    public function onShowStatusCodeInfo(): string
    {
        return (string) $this->makePartial('status_code_info', [], false);
    }

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

        if ($this->request->isMethod(Request::METHOD_GET)) {
            $this->formExtendRefreshFields($host, $fields);
        }
    }

    public function formExtendRefreshFields(Form $host, array $fields): void
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

    public function listInjectRowClass(Model $record): string
    {
        if ($record instanceof Models\Redirect
            && !$record->isActiveOnDate(Carbon::now())
        ) {
            return 'special';
        }

        return '';
    }

    /**
     * @throws ApplicationException
     * @throws SystemException
     */
    public function onTest(): array
    {
        $inputPath = $this->request->get('inputPath');
        $redirect = new Models\Redirect($this->request->get('Redirect'));

        try {
            $rule = RedirectRule::createWithModel($redirect);
            $manager = RedirectManager::createWithRule($rule);
            $testDate = Carbon::createFromFormat('Y-m-d', $this->request->get('test_date', date('Y-m-d')));
            $manager->setMatchDate($testDate);
            $match = $manager->match($inputPath, $this->request->get('test_scheme', $this->request->getScheme()));
        } catch (NoMatchForRequest | InvalidScheme | UnableToLoadRules $e) {
            $match = false;
        } catch (Throwable $e) {
            throw new ApplicationException($e->getMessage());
        }

        return [
            '#testResult' => $this->makePartial('redirect_test_result', [
                'match' => $match,
                'url' => $match && isset($manager) ? $manager->getLocation($match) : '',
            ]),
        ];
    }

    /**
     * @throws SystemException
     */
    public function onOpenRequestLog(): string
    {
        $this->makeLists();

        return $this->makePartial('request-log/modal');
    }

    /**
     * @throws ModelNotFoundException
     */
    public function onCreateRedirectFromRequestLogItems(): array
    {
        $checkedIds = $this->getCheckedIds();
        $redirectsCreated = 0;

        foreach ($checkedIds as $checkedId) {
            /** @var RequestLog $requestLog */
            $requestLog = RequestLog::query()->findOrFail($checkedId);

            $url = $this->parseRequestLogItemUrl((string) $requestLog->getAttribute('url'));

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

        if ((bool) $this->request->get('andDelete', false)) {
            RequestLog::destroy($checkedIds);
        }

        if ($redirectsCreated > 0) {
            $this->flash->success($this->translator->trans(
                'vdlp.redirect::lang.flash.success_created_redirects',
                [
                    'count' => $redirectsCreated,
                ]
            ));
        }

        return $this->listRefresh();
    }

    private function getCheckedIds(): array
    {
        if (($checkedIds = $this->request->get('checked'))
            && is_array($checkedIds)
            && count($checkedIds)
        ) {
            return array_map(static function ($checkedId) {
                return (int) $checkedId;
            }, $checkedIds);
        }

        return [];
    }

    private function getAllRedirectIds(): array
    {
        return Models\Redirect::query()
            ->get()
            ->pluck('id')
            ->toArray();
    }

    private function parseRequestLogItemUrl(string $url): string
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
