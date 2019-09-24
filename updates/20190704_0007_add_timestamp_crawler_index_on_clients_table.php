<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class AddTimestampCrawlerIndexOnClientsTable
 *
 * @package Vdlp\Redirect\Updates
 */
class AddTimestampCrawlerIndexOnClientsTable extends Migration
{
    public function up()
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

    public function down()
    {
        Schema::table('vdlp_redirect_clients', static function (Blueprint $table) {
            $table->dropIndex('timestamp_crawler');
        });
    }
}
