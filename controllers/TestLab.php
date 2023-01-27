<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Classes\Controller;
use Backend\Classes\NavigationManager;
use Carbon\Carbon;
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
    public $requiredPermissions = ['vdlp.redirect.access_redirects'];

    private array $redirects = [];

    public function __construct(
        private Request $request,
        private FlashBag $flash
    ) {
        $this->bodyClass = 'layout-relative';

        parent::__construct();

        NavigationManager::instance()->setContext('Vdlp.Redirect', 'redirect', 'test_lab');

        $this->loadRedirects();
    }

    public function index(): void
    {
        $this->pageTitle = 'vdlp.redirect::lang.title.test_lab';

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');
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
            ->filter(static function (Redirect $redirect): bool {
                return $redirect->isActiveOnDate(Carbon::today());
            })
            ->all());
    }

    private function offsetGetRedirect(int $offset): ?Redirect
    {
        return $this->redirects[$offset] ?? null;
    }

    public function onTest(): string
    {
        $offset = (int) $this->request->get('offset');

        $redirect = $this->offsetGetRedirect($offset);

        if ($redirect === null) {
            return '';
        }

        try {
            $partial = (string) $this->makePartial('tester_result', [
                'redirect' => $redirect,
                'testPath' => $this->getTestPath($redirect),
                'testResults' => $this->getTestResults($redirect, $this->request->secure()),
            ]);
        } catch (Throwable $e) {
            $partial = (string) $this->makePartial('tester_failed', [
                'redirect' => $redirect,
                'message' => $e->getMessage(),
            ]);
        }

        return $partial;
    }

    /**
     * @throws ModelNotFoundException
     */
    public function onReRun(): array
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::query()->findOrFail($this->request->get('id'));

        $this->flash->success(trans('vdlp.redirect::lang.test_lab.flash_test_executed'));

        return [
            '#testerResult' . $redirect->getKey() => $this->makePartial(
                'tester_result_items',
                $this->getTestResults($redirect, $this->request->secure())
            ),
        ];
    }

    /**
     * @throws ModelNotFoundException
     * @throws SystemException
     */
    public function onExclude(): array
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::query()->findOrFail($this->request->get('id'));
        $redirect->update(['test_lab' => false]);

        $this->flash->success(trans('vdlp.redirect::lang.test_lab.flash_redirect_excluded'));

        return [
            '#testButtonWrapper' => $this->makePartial('test_button', [
                'redirectCount' => $this->getRedirectCount(),
            ]),
        ];
    }

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

    public function getTestResults(Redirect $redirect, bool $secure): array
    {
        $testPath = $this->getTestPath($redirect);

        return [
            'maxRedirectsResult' => (new Testers\RedirectLoop($testPath, $secure))->execute(),
            'matchedRedirectResult' => (new Testers\RedirectMatch($testPath, $secure))->execute(),
            'responseCodeResult' => (new Testers\ResponseCode($testPath, $secure))->execute(),
            'redirectCountResult' => (new Testers\RedirectCount($testPath, $secure))->execute(),
            'finalDestinationResult' => (new Testers\RedirectFinalDestination($testPath, $secure))->execute(),
        ];
    }

    private function getRedirectCount(): int
    {
        return count($this->redirects);
    }
}
