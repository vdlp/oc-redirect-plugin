<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use Backend\Models\ExportModel;
use Eloquent;

/** @noinspection LongInheritanceChainInspection */

/**
 * Class RedirectExport
 *
 * @package Vdlp\Redirect\Models
 * @mixin Eloquent
 */
class RedirectExport extends ExportModel
{
    /**
     * {@inheritdoc}
     */
    public $table = 'vdlp_redirect_redirects';

    /**
     * {@inheritdoc}
     */
    public function exportData($columns, $sessionKey = null): array
    {
        return self::make()
            ->get()
            ->toArray();
    }
}
