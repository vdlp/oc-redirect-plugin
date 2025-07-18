<?php

declare(strict_types=1);

namespace Vdlp\Redirect\VueComponents;

use Backend\Helpers\Backend;
use Dashboard\Classes\DashReport;
use Dashboard\Classes\ReportFetchData;
use Dashboard\Classes\VueReportWidgetBase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use InvalidArgumentException;
use Vdlp\Redirect\Models\Redirect;

class CreateRedirect extends VueReportWidgetBase
{
    private Backend $backend;
    private Redirector $redirect;

    public function __construct($controller, DashReport $dashReport = null, array $properties = [])
    {
        parent::__construct($controller, $dashReport = null, $properties = []);

        $this->redirect = resolve(Redirector::class);
        $this->backend = resolve(Backend::class);
    }

    public function getData(ReportFetchData $data): array
    {
        return [
            'labels' => [
                'from_url' => e(trans('vdlp.redirect::lang.redirect.from_url')),
                'from_url_placeholder' => e(trans('vdlp.redirect::lang.redirect.from_url_placeholder')),
                'from_url_comment' => e(trans('vdlp.redirect::lang.redirect.from_url_comment')),
                'from_url_error' => e(trans('vdlp.redirect::lang.redirect.from_url_required_if')),
                'to_url' => e(trans('vdlp.redirect::lang.redirect.to_url')),
                'to_url_placeholder' => e(trans('vdlp.redirect::lang.redirect.to_url_placeholder')),
                'to_url_comment' => e(trans('vdlp.redirect::lang.redirect.to_url_comment')),
                'to_url_error' => e(trans('vdlp.redirect::lang.redirect.to_url_required_if')),
                'submit' => e(trans('vdlp.redirect::lang.buttons.create_redirect')),
            ],
        ];
    }

    protected function onSubmit(array $widgetConfig, array $extraData): RedirectResponse
    {
        $redirect = Redirect::create([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => $extraData['from_url'],
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => $extraData['to_url'],
            'to_scheme' => Redirect::SCHEME_AUTO,
            'test_url' => $extraData['from_url'],
            'requirements' => null,
            'status_code' => 302,
        ]);

        return $this->redirect->to($this->backend->url('vdlp/redirect/redirects/update/' . $redirect->getKey()));
    }
}
