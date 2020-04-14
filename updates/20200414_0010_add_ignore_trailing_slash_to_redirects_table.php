<?php

declare(strict_types=1);

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddIgnoreTrailingSlashToRedirectsTable extends Migration
{
    public function up(): void
    {
        Schema::table('vdlp_redirect_redirects', static function (Blueprint $table) {
            $table->boolean('ignore_trailing_slash')
                ->default(false)
                ->after('ignore_case');
        });
    }

    public function down(): void
    {
        Schema::table('vdlp_redirect_redirects', static function (Blueprint $table) {
            $table->dropColumn('ignore_trailing_slash');
        });
    }
}
