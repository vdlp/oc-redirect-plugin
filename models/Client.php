<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use Eloquent;
use October\Rain\Database\Model;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Client
 *
 * @package Vdlp\Redirect\Models
 * @mixin Eloquent
 */
class Client extends Model
{
    /**
     * {@inheritdoc}
     */
    public $table = 'vdlp_redirect_clients';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    public $belongsTo = [
        'redirect' => Redirect::class,
    ];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;
}
