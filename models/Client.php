<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use October\Rain\Database\Model;

final class Client extends Model
{
    public $belongsTo = [
        'redirect' => Redirect::class,
    ];

    public $timestamps = false;

    protected $table = 'vdlp_redirect_clients';

    protected $guarded = [];
}
