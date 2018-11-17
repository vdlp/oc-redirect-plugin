<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class AddRedirectTimestampCrawlerIndexOnClientsTable
 *
 * @package Vdlp\Redirect\Updates
 */
class AddRedirectTimestampCrawlerIndexOnClientsTable extends Migration
{
    public function up()
    {
        Schema::table('vdlp_redirect_clients', function (Blueprint $table) {
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

    public function down()
    {
        Schema::table('vdlp_redirect_clients', function (Blueprint $table) {
            $table->dropIndex('redirect_timestamp_crawler');
        });
    }
}
