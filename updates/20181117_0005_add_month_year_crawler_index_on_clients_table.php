<?php

/** @noinspection PhpUnused */
/** @noinspection AutoloadingIssuesInspection */

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddMonthYearCrawlerIndexOnClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('vdlp_redirect_clients', static function (Blueprint $table) {
            $table->index(
                [
                    'month',
                    'year',
                    'crawler'
                ],
                'month_year_crawler'
            );

            $table->index(
                [
                    'month',
                    'year',
                ],
                'month_year'
            );
        });
    }

    public function down(): void
    {
        Schema::table('vdlp_redirect_clients', static function (Blueprint $table) {
            $table->dropIndex('month_year_crawler');
            $table->dropIndex('month_year');
        });
    }
}
