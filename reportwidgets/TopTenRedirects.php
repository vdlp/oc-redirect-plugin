<?php

declare(strict_types=1);

namespace Vdlp\Redirect\ReportWidgets;

use SystemException;
use Vdlp\Redirect\Classes\StatisticsHelper;
use Backend\Classes\Controller;
use Backend\Classes\ReportWidgetBase;

/** @noinspection LongInheritanceChainInspection */

/**
 * Class TopTenRedirects
 *
 * @property string alias
 * @package Vdlp\Redirect\ReportWidgets
 */
class TopTenRedirects extends ReportWidgetBase
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Controller $controller, array $properties = [])
    {
        $this->alias = 'redirectTopTenRedirects';

        parent::__construct($controller, $properties);
    }

    /**
     * {@inheritdoc}
     * @throws SystemException
     */
    public function render()
    {
        $helper = new StatisticsHelper();

        return $this->makePartial('widget', [
            'topTenRedirectsThisMonth' => $helper->getTopRedirectsThisMonth()
        ]);
    }
}
