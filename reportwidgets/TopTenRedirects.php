<?php

declare(strict_types=1);

namespace Vdlp\Redirect\ReportWidgets;

use Backend\Classes\Controller;
use Backend\Classes\ReportWidgetBase;
use Vdlp\Redirect\Classes\StatisticsHelper;

/**
 * @property string $alias
 */
final class TopTenRedirects extends ReportWidgetBase
{
    public function __construct(Controller $controller, array $properties = [])
    {
        $this->alias = 'redirectTopTenRedirects';

        parent::__construct($controller, $properties);
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
