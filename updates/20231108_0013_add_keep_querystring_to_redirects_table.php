<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddKeepQueryStringToRedirectsTable extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('vdlp_redirect_redirects', 'keep_querystring')) {
            return;
        }

        Schema::table('vdlp_redirect_redirects', static function (Blueprint $table): void {
            $table->boolean('keep_querystring')
                ->default(false)
                ->after('ignore_trailing_slash');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('vdlp_redirect_redirects', 'keep_querystring')) {
            Schema::table('vdlp_redirect_redirects', static function (Blueprint $table): void {
                $table->dropColumn('keep_querystring');
            });
        }
    }
}
