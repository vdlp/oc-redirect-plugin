<?php

/** @noinspection PhpUnused */
/** @noinspection AutoloadingIssuesInspection */

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddTimestampCrawlerIndexOnClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('vdlp_redirect_clients', static function (Blueprint $table) {
            $table->index(
                [
                    'timestamp',
                    'crawler'
                ],
                'timestamp_crawler'
            );
        });
    }

    public function down(): void
    {
        Schema::table('vdlp_redirect_clients', static function (Blueprint $table) {
            $table->dropIndex('timestamp_crawler');
        });
    }
}
