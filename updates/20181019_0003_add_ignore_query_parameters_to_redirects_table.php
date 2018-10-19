<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class AddIgnoreQueryParametersToRedirectsTable
 *
 * @package Vdlp\Redirect\Updates
 */
class AddIgnoreQueryParametersToRedirectsTable extends Migration
{
    public function up()
    {
        Schema::table('vdlp_redirect_redirects', function (Blueprint $table) {
            $table->boolean('ignore_query_parameters')
                ->default(false)
                ->after('sort_order');
        });
    }

    public function down()
    {
        Schema::table('vdlp_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn('ignore_query_parameters');
        });
    }
}
