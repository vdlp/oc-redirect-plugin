<?php

declare(strict_types=1);

namespace Vdlp\Redirect\ReportWidgets;

use Backend\Classes\Controller;
use Dashboard\Classes\DashReport;
use Dashboard\Classes\ReportWidgetBase;
use Vdlp\Redirect\Classes\StatisticsHelper;

/**
 * @property string $alias
 */
final class TopTenRedirects extends ReportWidgetBase
{
    public function __construct(Controller $controller, DashReport $dashReport, array $properties = [])
    {
        $this->alias = 'redirectTopTenRedirects';

        parent::__construct($controller, $dashReport, $properties);
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title' => 'backend::lang.dashboard.widget_title_label',
                'default' => 'backend::lang.dashboard.welcome.widget_title_default',
                'type' => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error',
            ],
        ];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function render()
    {
        $helper = new StatisticsHelper();

        return $this->makePartial('widget', [
            'topTenRedirectsThisMonth' => $helper->getTopRedirectsThisMonth(),
        ]);
    }
}
