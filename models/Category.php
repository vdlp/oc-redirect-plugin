<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use Eloquent;
use October\Rain\Database\Model;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Category
 *
 * @package Vdlp\Redirect\Models
 * @mixin Eloquent
 */
class Category extends Model
{
    /**
     * {@inheritdoc}
     */
    public $table = 'vdlp_redirect_categories';
}
