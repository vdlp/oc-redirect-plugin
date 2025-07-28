<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use October\Rain\Database\Model;

final class RedirectLog extends Model
{
    public $belongsTo = [
        'redirect' => Redirect::class,
    ];

    protected $table = 'vdlp_redirect_redirect_logs';

    protected $guarded = [];
}
