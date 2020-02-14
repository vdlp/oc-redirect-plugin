<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use October\Rain\Database\Model;

final class Client extends Model
{
    /**
     * {@inheritDoc}
     */
    public $table = 'vdlp_redirect_clients';

    /**
     * {@inheritDoc}
     */
    protected $guarded = [];

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
