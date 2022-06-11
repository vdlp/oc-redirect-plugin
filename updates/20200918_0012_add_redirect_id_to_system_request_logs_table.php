<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddRedirectIdToSystemRequestLogsTable extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('system_request_logs', 'vdlp_redirect_redirect_id')) {
            return;
        }

        Schema::table('system_request_logs', static function (Blueprint $table): void {
            $table->unsignedInteger('vdlp_redirect_redirect_id')
                ->nullable()
                ->after('id');

            $table->foreign('vdlp_redirect_redirect_id', 'vdlp_redirect_request_log')
                ->references('id')
                ->on('vdlp_redirect_redirects')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('system_request_logs', 'vdlp_redirect_redirect_id')) {
            Schema::table('system_request_logs', static function (Blueprint $table): void {
                $table->dropForeign('vdlp_redirect_request_log');
                $table->dropColumn('vdlp_redirect_redirect_id');
            });
        }
    }
}
