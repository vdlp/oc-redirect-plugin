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

    /** @var string */
    private $redirectsFile;

    /**
     * {@inheritdoc}
     */
    protected function init()//: void
    {
        $this->redirectsFile = storage_path('app/redirects.csv');
    }

    /**
     * Publish applicable redirects.
     *
     * @return int Number of published redirects
     */
    public function publish(): int
    {
        if (file_exists($this->redirectsFile)) {
            unlink($this->redirectsFile);
        }

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
        ];

        /** @var Collection $redirects */
        $redirects = Redirect::query()
            ->where('is_enabled', '=', 1)
            ->orderBy('sort_order')
            ->get($columns);

        try {
            $writer = Writer::createFromPath($this->redirectsFile, 'w+');
            $writer->insertOne($columns);

            foreach ($redirects->toArray() as $row) {
                if (array_key_exists('requirements', $row)) {
                    $row['requirements'] = json_encode($row['requirements']);
                }

                $writer->insertOne($row);
            }
        } catch (Exception $e) {
            Log::critical($e);
        }

        return $redirects->count();
    }
}
