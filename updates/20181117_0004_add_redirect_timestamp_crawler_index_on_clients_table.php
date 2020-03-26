<?php

/** @noinspection PhpUnused */
/** @noinspection AutoloadingIssuesInspection */

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddRedirectTimestampCrawlerIndexOnClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('vdlp_redirect_clients', static function (Blueprint $table) {
            $table->index(
                [
                    'redirect_id',
                    'timestamp',
                    'crawler'
                ],
                'redirect_timestamp_crawler'
            );
        });
    }

    public function down(): void
    {
        Schema::table('vdlp_redirect_clients', static function (Blueprint $table) {
            $table->dropIndex('redirect_timestamp_crawler');
        });
    }
}
