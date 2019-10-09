<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use Eloquent;
use October\Rain\Database\Model;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class RedirectLog
 *
 * @package Vdlp\Redirect\Models
 * @mixin Eloquent
 */
class RedirectLog extends Model
{
    /**
     * {@inheritDoc}
     */
    public $table = 'vdlp_redirect_redirect_logs';

    /**
     * {@inheritDoc}
     */
    protected $guarded = [];

    /**
     * {@inheritDoc}
     */
    public $dates = [
        'date_time',
    ];

    /**
     * {@inheritDoc}
     */
    public $belongsTo = [
        'redirect' => Redirect::class,
    ];

    /**
     * {@inheritDoc}
     */
    public $timestamps = false;
}
