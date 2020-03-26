<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use October\Rain\Database\Model;

final class RedirectLog extends Model
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
