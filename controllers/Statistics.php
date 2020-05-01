<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use SystemException;
use Vdlp\Redirect\Classes\StatisticsHelper;

/**
 * @property string $pageTitle
 */
final class Statistics extends Controller
{
    /**
     * @var StatisticsHelper
     */
    private $helper;

    /**
     * @var array
     */
    public $requiredPermissions = ['vdlp.redirect.access_redirects'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', 'statistics');

        $this->pageTitle = 'vdlp.redirect::lang.title.statistics';

        $this->addJs('https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js');
        $this->addCss('https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css');
        $this->addJs('/plugins/vdlp/redirect/assets/javascript/statistics.js');
        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');
        $this->addJs('/plugins/vdlp/redirect/assets/javascript/redirect.js');
        $this->addCss('/plugins/vdlp/redirect/assets/css/statistics.css');

        $this->helper = new StatisticsHelper();
    }

    public function index(): void
    {
    }

    public function onRedirectHitsPerDay(): string
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
     * @throws SystemException
     */
    public function onLoadTopRedirectsThisMonth(): array
    {
        return [
            '#topRedirectsThisMonth' => $this->makePartial('top-redirects-this-month', [
                'topTenRedirectsThisMonth' => $this->helper->getTopRedirectsThisMonth(),
            ]),
        ];
    }

    /**
     * @throws SystemException
     */
    public function onLoadTopCrawlersThisMonth(): array
    {
        return [
            '#topCrawlersThisMonth' => $this->makePartial('top-crawlers-this-month', [
                'topTenCrawlersThisMonth' => $this->helper->getTopTenCrawlersThisMonth(),
            ]),
        ];
    }

    /**
     * @throws SystemException
     */
    public function onLoadRedirectHitsPerMonth(): array
    {
        return [
            '#redirectHitsPerMonth' => $this->makePartial('redirect-hits-per-month', [
                'redirectHitsPerMonth' => $this->helper->getRedirectHitsPerMonth(),
            ]),
        ];
    }

    /**
     * @throws SystemException
     */
    public function onLoadScoreBoard(): array
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
}
