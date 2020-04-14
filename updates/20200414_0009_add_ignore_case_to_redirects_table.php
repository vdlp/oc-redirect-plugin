<?php

declare(strict_types=1);

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddIgnoreCaseToRedirectsTable extends Migration
{
    public function up(): void
    {
        Schema::table('vdlp_redirect_redirects', static function (Blueprint $table) {
            $table->boolean('ignore_case')
                ->default(false)
                ->after('ignore_query_parameters');
        });
    }

    public function down(): void
    {
        Schema::table('vdlp_redirect_redirects', static function (Blueprint $table) {
            $table->dropColumn('ignore_case');
        });
    }
}
