<?php

declare(strict_types=1);

namespace Vdlp\Redirect\ReportWidgets;

use Backend\Classes\Controller;
use Backend\Classes\ReportWidgetBase;
use Backend\Helpers\Backend;
use Backend\Widgets\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Vdlp\Redirect\Models\Redirect;

/**
 * @property string $alias
 */
final class CreateRedirect extends ReportWidgetBase
{
    private Redirector $redirect;

    public function __construct(Controller $controller, array $properties = [])
    {
        $this->alias = 'redirectCreateRedirect';

        parent::__construct($controller, $properties);

        $this->redirect = resolve(Redirector::class);
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function render()
    {
        $widgetConfig = $this->makeConfig('~/plugins/vdlp/redirect/reportwidgets/createredirect/fields.yaml');
        $widgetConfig->model = new Redirect;
        $widgetConfig->alias = $this->alias . 'Redirect';

        $this->vars['formWidget'] = $this->makeWidget(Form::class, $widgetConfig);

        return $this->makePartial('widget');
    }

    public function onSubmit(): RedirectResponse
    {
        $redirect = Redirect::create([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => post('from_url'),
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => post('to_url'),
            'to_scheme' => Redirect::SCHEME_AUTO,
            'test_url' => post('from_url'),
            'requirements' => null,
            'status_code' => 302,
        ]);

        return $this->redirect->to(Backend::url('vdlp/redirect/redirects/update/' . $redirect->getKey()));
    }
}
