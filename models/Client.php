<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use October\Rain\Database\Model;

final class Client extends Model
{
    public $table = 'vdlp_redirect_clients';

    public $belongsTo = [
        'redirect' => Redirect::class,
    ];

    public $timestamps = false;

    protected $guarded = [];
}
