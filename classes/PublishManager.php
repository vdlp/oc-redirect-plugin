<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Illuminate\Database\Eloquent\Collection;
use JsonException;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;
use Throwable;
use Vdlp\Redirect\Classes\Contracts\CacheManagerInterface;
use Vdlp\Redirect\Classes\Contracts\PublishManagerInterface;
use Vdlp\Redirect\Models\Redirect;

final class PublishManager implements PublishManagerInterface
{
    public function __construct(
        private LoggerInterface $log,
        private CacheManagerInterface $cacheManager
    ) {
    }

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
            'ignore_query_parameters',
            'ignore_case',
            'ignore_trailing_slash',
        ];

        /** @var Collection $redirects */
        $redirects = Redirect::query()
            ->where('is_enabled', 1)
            ->orderBy('sort_order')
            ->get($columns);

        if ($this->cacheManager->cachingEnabledAndSupported()) {
            $this->publishToCache($redirects->toArray());
        } else {
            $this->publishToFilesystem($columns, $redirects->toArray());
        }

        $count = $redirects->count();

        if ((bool) config('vdlp.redirect::log_redirect_changes', false) === true) {
            $this->log->info(sprintf(
                'Vdlp.Redirect: Redirect engine has been updated with %s redirects.',
                $count
            ));
        }

        return $count;
    }

    private function publishToFilesystem(array $columns, array $redirects): void
    {
        $redirectsFile = config('vdlp.redirect::rules_path');

        if (file_exists($redirectsFile)) {
            unlink($redirectsFile);
        }

        try {
            $writer = Writer::createFromPath($redirectsFile, 'w+');
            $writer->insertOne($columns);

            foreach ($redirects as $row) {
                if (isset($row['requirements'])) {
                    $row['requirements'] = json_encode($row['requirements'], JSON_THROW_ON_ERROR);
                }

                $writer->insertOne($row);
            }
        } catch (Throwable $throwable) {
            touch($redirectsFile);

            $this->log->error($throwable);
        }
    }

    private function publishToCache(array $redirects): void
    {
        foreach ($redirects as &$redirect) {
            if (!isset($redirect['requirements'])) {
                continue;
            }

            try {
                $redirect['requirements'] = json_encode($redirect['requirements'], JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                // @ignoreException
            }
        }

        unset($redirect);

        $this->cacheManager->flush();
        $this->cacheManager->putRedirectRules($redirects);
    }
}
