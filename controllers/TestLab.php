<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Carbon\Carbon;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use October\Rain\Database\Collection;
use October\Rain\Flash\FlashBag;
use SystemException;
use Throwable;
use Vdlp\Redirect\Classes\Testers;
use Vdlp\Redirect\Models\Redirect;

/**
 * @property string $bodyClass
 */
final class TestLab extends Controller
{
    /**
     * @var array
     */
    public $requiredPermissions = ['vdlp.redirect.access_redirects'];

    /**
     * @var array
     */
    private $redirects;

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

    public function __construct(Request $request, Translator $translator)
    {
        $this->bodyClass = 'layout-relative';

        parent::__construct();

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', 'test_lab');

        $this->loadRedirects();

        $this->request = $request;
        $this->translator = $translator;
        $this->flash = resolve('flash');
    }

    public function index(): void
    {
        $this->pageTitle = 'vdlp.redirect::lang.title.test_lab';

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');
        $this->addJs('/plugins/vdlp/redirect/assets/javascript/redirect.js');
        $this->addCss('/plugins/vdlp/redirect/assets/css/test-lab.css');
        $this->addJs('/plugins/vdlp/redirect/assets/javascript/test-lab.js');

        $this->vars['redirectCount'] = $this->getRedirectCount();
    }

    private function loadRedirects(): void
    {
        /** @var Collection $redirects */
        $this->redirects = array_values(Redirect::enabled()
            ->testLabEnabled()
            ->orderBy('sort_order')
            ->get()
            ->filter(static function (Redirect $redirect) {
                return $redirect->isActiveOnDate(Carbon::today());
            })
            ->all());
    }

    private function offsetGetRedirect(int $offset): ?Redirect
    {
        return $this->redirects[$offset] ?? null;
    }

    // @codingStandardsIgnoreStart

    /**
     * @throws SystemException
     */
    public function index_onTest(): string
    {
        $offset = (int) $this->request->get('offset');

        $redirect = $this->offsetGetRedirect($offset);

        if ($redirect === null) {
            return '';
        }

        try {
            $partial = (string) $this->makePartial(
                'tester_result', [
                    'redirect' => $redirect,
                    'testPath' => $this->getTestPath($redirect),
                    'testResults' => $this->getTestResults($redirect),
                ]
            );
        } catch (Throwable $e) {
            $partial = (string) $this->makePartial(
                'tester_failed',
                [
                    'redirect' => $redirect,
                    'message' => $e->getMessage()
                ]
            );
        }

        return $partial;
    }

    /**
     * @throws ModelNotFoundException
     * @throws SystemException
     */
    public function index_onReRun(): array
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::query()->findOrFail($this->request->get('id'));

        $this->flash->success($this->translator->trans('vdlp.redirect::lang.test_lab.flash_test_executed'));

        return [
            '#testerResult' . $redirect->getKey() => $this->makePartial(
                'tester_result_items',
                $this->getTestResults($redirect)
            )
        ];
    }

    /**
     * @throws ModelNotFoundException
     * @throws SystemException
     */
    public function index_onExclude(): array
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::query()->findOrFail($this->request->get('id'));
        $redirect->update(['test_lab' => false]);

        $this->flash->success($this->translator->trans('vdlp.redirect::lang.test_lab.flash_redirect_excluded'));

        return [
            '#testButtonWrapper' => $this->makePartial(
                'test_button',
                [
                    'redirectCount' => $this->getRedirectCount()
                ]
            )
        ];
    }

    // @codingStandardsIgnoreEnd

    public function getTestPath(Redirect $redirect): string
    {
        $testPath = '/';

        if ($redirect->isMatchTypeExact()) {
            $testPath = (string) $redirect->getAttribute('from_url');
        } elseif ($redirect->getAttribute('test_lab_path')) {
            $testPath = (string) $redirect->getAttribute('test_lab_path');
        }

        return $testPath;
    }

    public function getTestResults(Redirect $redirect): array
    {
        $testPath = $this->getTestPath($redirect);

        return [
            'maxRedirectsResult' => (new Testers\RedirectLoop($testPath))->execute(),
            'matchedRedirectResult' => (new Testers\RedirectMatch($testPath))->execute(),
            'responseCodeResult' => (new Testers\ResponseCode($testPath))->execute(),
            'redirectCountResult' => (new Testers\RedirectCount($testPath))->execute(),
            'finalDestinationResult' => (new Testers\RedirectFinalDestination($testPath))->execute(),
        ];
    }

    private function getRedirectCount(): int
    {
        return count($this->redirects);
    }
}
