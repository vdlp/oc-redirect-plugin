<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use Backend\Models\ImportModel;
use Throwable;
use Vdlp\Redirect\Classes\PublishManager;

final class RedirectImport extends ImportModel
{
    /**
     * {@inheritDoc}
     */
    public $table = 'vdlp_redirect_redirects';

    /**
     * Basic validation rules.
     * More (conditional) rules will be applied when importing.
     *
     * @var array
     */
    public $rules = [
        'from_url' => 'required',
        'match_type' => 'required|in:exact,placeholders,regex',
        'target_type' => 'required|in:path_or_url,cms_page,static_page,none',
        'status_code' => 'required|in:301,302,303,404,410',
    ];

    /**
     * @var array
     */
    private static $nullableAttributes = [
        'category_id',
        'from_date',
        'to_date',
        'last_used_at',
        'to_url',
        'test_url',
        'cms_page',
        'static_page',
        'requirements',
        'test_lab_path',
    ];

    /**
     * {@inheritDoc}
     */
    public function importData($results, $sessionKey = null)
    {
        foreach ((array) $results as $row => $data) {
            try {
                $source = Redirect::make();

                $except = ['id'];

                foreach (array_except($data, $except) as $attribute => $value) {
                    if ($attribute === 'requirements') {
                        $value = json_decode($value, true);
                    } elseif (empty($value) && in_array($attribute, self::$nullableAttributes, true)) {
                        $value = null;
                    }

                    $source->setAttribute($attribute, $value);
                }

                $source->save();

                $this->logCreated();
            } catch (Throwable $e) {
                $this->logError($row, $e->getMessage());
            }
        }

        $createCount = $this->resultStats['created'] ?? 0;

        if ($createCount > 0) {
            /** @var PublishManager $publishManager */
            $publishManager = resolve(PublishManager::class);
            $publishManager->publish();
        }
    }
}
