<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Vdlp\Redirect\Classes\StatisticsHelper;
use Backend\Classes\Controller;
use BackendMenu;
use SystemException;

/**
 * Class Statistics
 *
 * @property string pageTitle
 * @package Vdlp\Redirect\Controllers
 */
class Statistics extends Controller
{
    /** @var StatisticsHelper */
    private $helper;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', 'statistics');

        $this->pageTitle = 'vdlp.redirect::lang.title.statistics';

        $this->addJs('https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js');
        $this->addJs('/plugins/vdlp/redirect/assets/javascript/statistics.js');

        $this->addCss('https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css');
        $this->addCss('/plugins/vdlp/redirect/assets/css/statistics.css');

        $this->helper = new StatisticsHelper();
    }

    /**
     * @return void
     */
    public function index()//: void
    {
    }

    // @codingStandardsIgnoreStart

    /**
     * @return string
     */
    public function index_onRedirectHitsPerDay(): string
    {
        $crawlerHits = $this->helper->getRedirectHitsPerDay(true);

        $data = [];

        foreach ($crawlerHits as $hit) {
            $data[] = [
                'x' => date('Y-m-d', mktime(0, 0, 0, (int) $hit['month'], (int) $hit['day'], (int) $hit['year'])),
                'y' => (int) $hit['hits'],
                'group' => 0
            ];
        }

        $notCrawlerHits = $this->helper->getRedirectHitsPerDay();

        foreach ($notCrawlerHits as $hit) {
            $data[] = [
                'x' => date('Y-m-d', mktime(0, 0, 0, (int) $hit['month'], (int) $hit['day'], (int) $hit['year'])),
                'y' => (int) $hit['hits'],
                'group' => 1
            ];
        }


        return json_encode($data);
    }

    /**
     * @return array
     * @throws SystemException
     */
    public function index_onLoadTopRedirectsThisMonth(): array
    {
        return [
            '#topRedirectsThisMonth' => $this->makePartial('top-redirects-this-month', [
                'topTenRedirectsThisMonth' => $this->helper->getTopRedirectsThisMonth(),
            ]),
        ];
    }

    /**
     * @return array
     * @throws SystemException
     */
    public function index_onLoadTopCrawlersThisMonth(): array
    {
        return [
            '#topCrawlersThisMonth' => $this->makePartial('top-crawlers-this-month', [
                'topTenCrawlersThisMonth' => $this->helper->getTopTenCrawlersThisMonth(),
            ]),
        ];
    }

    /**
     * @return array
     * @throws SystemException
     */
    public function index_onLoadRedirectHitsPerMonth(): array
    {
        return [
            '#redirectHitsPerMonth' => $this->makePartial('redirect-hits-per-month', [
                'redirectHitsPerMonth' => $this->helper->getRedirectHitsPerMonth(),
            ]),
        ];
    }

    /**
     * @return array
     * @throws SystemException
     */
    public function index_onLoadScoreBoard(): array
    {
        return [
            '#scoreBoard' => $this->makePartial('score-board', [
                'redirectHitsPerMonth' => $this->helper->getRedirectHitsPerMonth(),
                'totalActiveRedirects' => $this->helper->getTotalActiveRedirects(),
                'activeRedirects' => $this->helper->getActiveRedirects(),
                'totalRedirectsServed' => $this->helper->getTotalRedirectsServed(),
                'totalThisMonth' => $this->helper->getTotalThisMonth(),
                'totalLastMonth' => $this->helper->getTotalLastMonth(),
                'latestClient' => $this->helper->getLatestClient(),
            ]),
        ];
    }

    // @codingStandardsIgnoreEnd
}
