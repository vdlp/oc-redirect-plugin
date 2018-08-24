<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use Backend\Models\ImportModel;
use Eloquent;
use Event;
use Exception;

/** @noinspection LongInheritanceChainInspection */

/**
 * Class RedirectImport
 *
 * @package Vdlp\Redirect\Models
 * @mixin Eloquent
 */
class RedirectImport extends ImportModel
{
    /**
     * {@inheritdoc}
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
        'match_type' => 'required|in:exact,placeholders',
        'target_type' => 'required|in:path_or_url,cms_page,static_page,none',
        'status_code' => 'required|in:301,302,303,404,410',
    ];

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
     * {@inheritdoc}
     */
    public function importData($results, $sessionKey = null)//: void
    {
        foreach ((array) $results as $row => $data) {
            try {
                $source = Redirect::make();

                $except = ['id'];

                foreach (array_except($data, $except) as $attribute => $value) {
                    if ($attribute === 'requirements') {
                        $value = json_decode($value);
                    } elseif (empty($value) && in_array($attribute, self::$nullableAttributes, true)) {
                        $value = null;
                    }

                    $source->setAttribute($attribute, $value);
                }

                $source->save();

                $this->logCreated();
            } catch (Exception $e) {
                $this->logError($row, $e->getMessage());
            }
        }

        Event::fire('redirects.changed');
    }
}
