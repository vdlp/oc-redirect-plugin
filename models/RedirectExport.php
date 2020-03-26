<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Models;

use Backend\Models\ExportModel;

final class RedirectExport extends ExportModel
{
    /**
     * {@inheritDoc}
     */
    public $table = 'vdlp_redirect_redirects';

    /**
     * {@inheritDoc}
     */
    public function exportData($columns, $sessionKey = null)
    {
        return static::make()
            ->get()
            ->toArray();
    }
}
