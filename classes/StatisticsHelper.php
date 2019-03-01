<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Carbon\Carbon;
use DB;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use October\Rain\Database\Collection;
use Vdlp\Redirect\Models;

/**
 * Class StatisticsHelper
 *
 * @package Vdlp\Redirect\Classes
 */
class StatisticsHelper
{
    /**
     * @return int
     */
    public function getTotalRedirectsServed(): int
    {
        return Models\Client::count();
    }

    /**
     * @param int|null $redirectId
     * @return Models\Client|null
     */
    public function getLatestClient(int $redirectId = null)//: ?Client
    {
        $builder = Models\Client::query()
            ->orderBy('timestamp', 'desc')
            ->limit(1);

        if ($redirectId) {
            $builder->where('redirect_id', '=', $redirectId);
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $builder->first();
    }

    /**
     * @param int|null $redirectId
     * @return int
     */
    public function getTotalThisMonth(int $redirectId = null): int
    {
        $builder = Models\Client::query()
            ->where('month', '=', date('m'))
            ->where('year', '=', date('Y'));

        if ($redirectId) {
            $builder->where('redirect_id', '=', $redirectId);
        }

        return $builder->count();
    }

    /**
     * @param int|null $redirectId
     * @return int
     */
    public function getTotalLastMonth(int $redirectId = null): int
    {
        $lastMonth = Carbon::today();
        $lastMonth->subMonthNoOverflow();

        $builder = Models\Client::query()
            ->where('month', '=', $lastMonth->month)
            ->where('year', '=', $lastMonth->year);

        if ($redirectId) {
            $builder->where('redirect_id', '=', $redirectId);
        }

        return $builder->count();
    }

    /**
     * @return array
     */
    public function getActiveRedirects(): array
    {
        $groupedRedirects = [];

        /** @var Collection $redirects */
        $redirects = Models\Redirect::enabled()
            ->get()
            ->filter(function (Models\Redirect $redirect) {
                return $redirect->isActiveOnDate(Carbon::today());
            });

        /** @var Models\Redirect $redirect */
        foreach ($redirects as $redirect) {
            $groupedRedirects[$redirect->getAttribute('status_code')][] = $redirect;
        }

        return $groupedRedirects;
    }

    /**
     * @return int
     */
    public function getTotalActiveRedirects(): int
    {
        return Models\Redirect::enabled()
            ->get()
            ->filter(function (Models\Redirect $redirect) {
                return $redirect->isActiveOnDate(Carbon::today());
            })
            ->count();
    }

    /**
     * @param bool $crawler
     * @return array
     */
    public function getRedirectHitsPerDay($crawler = false): array
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $result = Models\Client::selectRaw('COUNT(id) AS hits')
            ->addSelect('day', 'month', 'year')
            ->groupBy('day', 'month', 'year')
            ->orderByRaw('year ASC, month ASC, day ASC');

        if ($crawler) {
            $result->whereNotNull('crawler');
        } else {
            $result->whereNull('crawler');
        }

        return $result->limit(365)
            ->get()
            ->toArray();
    }

    /**
     * Gets the data for the 30d sparkline graph.
     *
     * @param int $redirectId
     * @param bool $crawler
     * @param int $days
     * @return array
     */
    public function getRedirectHitsSparkline(int $redirectId, bool $crawler, int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days);

        // DB index: redirect_timestamp_crawler
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $builder = Models\Client::selectRaw('COUNT(id) AS hits, DATE(timestamp) AS date')
            ->where('redirect_id', '=', $redirectId)
            ->groupBy('day', 'month', 'year', 'timestamp')
            ->orderByRaw('year ASC, month ASC, day ASC')
            ->where('timestamp', '>=', $startDate->toDateTimeString());

        if ($crawler) {
            $builder->whereNotNull('crawler');
        } else {
            $builder->whereNull('crawler');
        }

        $result = $builder
            ->get()
            ->keyBy('date')
            ->toArray();

        $hits = [];

        while ($startDate->lt(Carbon::now())) {
            if (isset($result[$startDate->toDateString()])) {
                $hits[] = (int) $result[$startDate->toDateString()]['hits'];
            } else {
                $hits[] = 0;
            }

            $startDate->addDay();
        }

        return $hits;
    }

    /**
     * @return array
     */
    public function getRedirectHitsPerMonth(): array
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (array) Models\Client::selectRaw('COUNT(id) AS hits')
            ->addSelect('month', 'year')
            ->groupBy('month', 'year')
            ->orderByRaw('year DESC, month DESC')
            ->limit(12)
            ->get()
            ->toArray();
    }

    /**
     * @return array
     */
    public function getTopTenCrawlersThisMonth(): array
    {
        // DB index: month_year_crawler
        return (array) Models\Client::selectRaw('COUNT(id) AS hits')
            ->addSelect('crawler')
            ->where('month', '=', (int) date('n'))
            ->where('year', '=', (int) date('Y'))
            ->whereNotNull('crawler')
            ->groupBy('crawler')
            ->orderByRaw('hits DESC')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getTopRedirectsThisMonth(int $limit = 10): array
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return (array) Models\Client::selectRaw('COUNT(redirect_id) AS hits')
            ->addSelect('redirect_id', 'r.from_url')
            ->join('vdlp_redirect_redirects AS r', 'r.id', '=', 'redirect_id')
            ->where('month', '=', (int) date('n'))
            ->where('year', '=', (int) date('Y'))
            ->groupBy('redirect_id', 'r.from_url')
            ->orderByRaw('hits DESC')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Update database hits statistics for given Redirect.
     *
     * @param int $redirectId
     */
    public function increaseHitsForRedirect(int $redirectId)//: void
    {
        /** @var Models\Redirect $redirect */
        $redirect = Models\Redirect::find($redirectId);

        if ($redirect === null) {
            return;
        }

        $now = Carbon::now();

        /** @noinspection PhpUndefinedClassInspection */
        $redirect->update([
            'hits' => DB::raw('hits + 1'),
            'last_used_at' => $now,
        ]);

        $crawlerDetect = new CrawlerDetect();

        Models\Client::create([
            'redirect_id' => $redirectId,
            'timestamp' => $now,
            'day' => $now->day,
            'month' => $now->month,
            'year' => $now->year,
            'crawler' => $crawlerDetect->isCrawler() ? $crawlerDetect->getMatches() : null,
        ]);
    }
}
