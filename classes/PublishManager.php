<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Vdlp\Redirect\Models\Redirect;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use League\Csv\Writer;
use Log;
use October\Rain\Support\Traits\Singleton;

/**
 * Class PublishManager
 *
 * @package Vdlp\Redirect\Classes
 */
class PublishManager
{
    use Singleton;

    /**
     * Publish applicable redirects.
     *
     * @return int Number of published redirects
     */
    public function publish(): int
    {
        $columns = [
            'id',
            'match_type',
            'target_type',
            'from_scheme',
            'from_url',
            'to_scheme',
            'to_url',
            'cms_page',
            'static_page',
            'status_code',
            'requirements',
            'from_date',
            'to_date',
            'ignore_query_parameters'
        ];

        /** @var Collection $redirects */
        $redirects = Redirect::query()
            ->where('is_enabled', '=', 1)
            ->orderBy('sort_order')
            ->get($columns);

        if (CacheManager::cachingEnabledAndSupported()) {
            $this->publishToCache($redirects->toArray());
        } else {
            $this->publishToFilesystem($columns, $redirects->toArray());
        }

        return $redirects->count();
    }

    /**
     * @param array $columns
     * @param array $redirects
     */
    private function publishToFilesystem(array $columns, array $redirects)
    {
        $redirectsFile = storage_path('app/redirects.csv');

        if (file_exists($redirectsFile)) {
            unlink($redirectsFile);
        }

        try {
            $writer = Writer::createFromPath($redirectsFile, 'w+');
            $writer->insertOne($columns);

            foreach ($redirects as $row) {
                if (isset($row['requirements'])) {
                    $row['requirements'] = json_encode($row['requirements']);
                }

                $writer->insertOne($row);
            }
        } catch (Exception $e) {
            Log::critical($e);
        }
    }

    /**
     * @param array $redirects
     */
    private function publishToCache(array $redirects)
    {
        foreach ($redirects as &$redirect) {
            if (isset($redirect['requirements'])) {
                $redirect['requirements'] = json_encode($redirect['requirements']);
            }

        }

        unset($redirect);

        CacheManager::instance()->putRedirectRules($redirects);
    }
}
