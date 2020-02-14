<?php

/** @noinspection PhpUnused */
/** @noinspection AutoloadingIssuesInspection */

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddDescriptionToRedirectsTable extends Migration
{
    public function up(): void
    {
        Schema::table('vdlp_redirect_redirects', static function (Blueprint $table) {
            $table->string('description')
                ->nullable()
                ->after('system');
        });
    }

    public function down(): void
    {
        Schema::table('vdlp_redirect_redirects', static function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
