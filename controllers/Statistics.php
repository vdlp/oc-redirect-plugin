<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Classes\Controller;
use Backend\Models\BrandSetting;
use BackendMenu;
use Carbon\Carbon;
use JsonException;
use SystemException;
use Vdlp\Redirect\Classes\StatisticsHelper;

/**
 * @property string $pageTitle
 */
final class Statistics extends Controller
{
    public $requiredPermissions = ['vdlp.redirect.access_redirects'];
    private StatisticsHelper $helper;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Vdlp.Redirect', 'redirect', 'statistics');

        $this->pageTitle = 'vdlp.redirect::lang.title.statistics';

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');
        $this->addCss('/plugins/vdlp/redirect/assets/css/statistics.css');

        $this->helper = new StatisticsHelper();
    }

    public function index(): void
    {
    }

    /**
     * @throws SystemException|JsonException
     */
    public function onLoadHitsPerDay(): array
    {
        $today = Carbon::today();

        $postValue = post('period-month-year', $today->month . '_' . $today->year);

        [$month, $year] = explode('_', $postValue);

        return [
            '#hitsPerDay' => $this->makePartial('hits-per-day', [
                'dataSets' => json_encode([
                    $this->getHitsPerDayAsDataSet((int) $month, (int) $year, true),
                    $this->getHitsPerDayAsDataSet((int) $month, (int) $year, false),
                ], JSON_THROW_ON_ERROR),
                'labels' => json_encode($this->getLabels(), JSON_THROW_ON_ERROR),
                'monthYearOptions' => $this->helper->getMonthYearOptions(),
                'monthYearSelected' => $month . '_' . $year,
            ]),
        ];
    }

    /**
     * @throws SystemException|JsonException
     */
    public function onSelectPeriodMonthYear(): array
    {
        return $this->onLoadHitsPerDay();
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

    private function getLabels(): array
    {
        $labels = [];

        foreach (Carbon::today()->firstOfMonth()->daysUntil(Carbon::today()->endOfMonth()) as $date) {
            $labels[] = $date->isoFormat('LL');
        }

        return $labels;
    }

    private function getHitsPerDayAsDataSet(int $month, int $year, bool $crawler): array
    {
        $today = Carbon::createFromDate($year, $month, 1);

        $data = $this->helper->getRedirectHitsPerDay($month, $year, $crawler);

        for ($i = $today->firstOfMonth()->day; $i <= $today->lastOfMonth()->day; $i++) {
            if (!array_key_exists($i, $data)) {
                $data[$i] = ['hits' => 0];
            }
        }

        ksort($data);

        $brandSettings = new BrandSetting();

        $color = $crawler ? $brandSettings->get('primary_color') : $brandSettings->get('secondary_color');

        return [
            'label' => $crawler ? 'Crawler hits' : 'Visitor hits',
            'backgroundColor' => $color,
            'borderColor' => $color,
            'data' => data_get($data, '*.hits'),
        ];
    }
}
