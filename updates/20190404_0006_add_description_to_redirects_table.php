<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class AddDescriptionToRedirectsTable
 *
 * @package Vdlp\Redirect\Updates
 */
class AddDescriptionToRedirectsTable extends Migration
{
    public function up()
    {
        Schema::table('vdlp_redirect_redirects', function (Blueprint $table) {
            $table->string('description')
                ->nullable()
                ->after('system');
        });
    }

    public function down()
    {
        Schema::table('vdlp_redirect_redirects', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
