<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Controllers;

use Backend\Classes\Controller;
use Backend\Classes\NavigationManager;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use JsonException;
use SystemException;
use Vdlp\Redirect\Classes\BrandHelper;
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

        NavigationManager::instance()->setContext('Vdlp.Redirect', 'redirect', 'statistics');

        $this->pageTitle = 'vdlp.redirect::lang.title.statistics';

        $this->addCss('/plugins/vdlp/redirect/assets/css/redirect.css');
        $this->addCss('/plugins/vdlp/redirect/assets/css/statistics.css');

        $this->helper = new StatisticsHelper();
    }

    public function index(): void
    {
    }

    /**
     * @throws SystemException|JsonException|InvalidFormatException
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
                'labels' => json_encode($this->getLabels((int) $month, (int) $year), JSON_THROW_ON_ERROR),
                'monthYearOptions' => $this->helper->getMonthYearOptions(),
                'monthYearSelected' => $month . '_' . $year,
            ]),
        ];
    }

    /**
     * @throws InvalidFormatException
     * @throws JsonException
     * @throws SystemException
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

    private function getLabels(int $month, int $year): array
    {
        $labels = [];

        $dates = Carbon::create($year, $month)
            ->firstOfMonth()
            ->daysUntil(Carbon::create($year, $month)->endOfMonth());

        foreach ($dates as $date) {
            $labels[] = $date->isoFormat('LL');
        }

        return $labels;
    }

    /**
     * @throws InvalidFormatException
     */
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

        $color = BrandHelper::instance()->getPrimaryOrSecondaryColor($crawler);

        [$r, $g, $b] = sscanf($color, "#%02x%02x%02x");

        return [
            'label' => $crawler
                ? e(trans('vdlp.redirect::lang.statistics.crawler_hits'))
                : e(trans('vdlp.redirect::lang.statistics.visitor_hits')),
            'backgroundColor' => sprintf('rgb(%d, %d, %d, 0.5)', $r, $g, $b),
            'borderColor' => sprintf('rgb(%d, %d, %d, 1)', $r, $g, $b),
            'borderWidth' => 1,
            'data' => data_get($data, '*.hits'),
        ];
    }
}
