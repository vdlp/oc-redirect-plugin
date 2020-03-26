<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use October\Rain\Database\Model;

final class Category extends Model
{
    /**
     * {@inheritDoc}
     */
    public $table = 'vdlp_redirect_categories';
}
