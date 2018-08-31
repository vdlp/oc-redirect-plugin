<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use Exception;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;
use Vdlp\Redirect\Classes\PublishManager;
use Vdlp\Redirect\Models\Category;
use Vdlp\Redirect\Models\Redirect;
use Vdlp\Redirect\Models\Settings;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class CreateTables
 *
 * @package Vdlp\Redirect\Updates
 */
class CreateTables extends Migration
{
    public function up()
    {
        Schema::create('vdlp_redirect_categories', function (Blueprint $table) {
            // Table configuration
            $table->engine = 'InnoDB';

            // Columns
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        // TODO: Multi-lingual
        Category::create(['name' => 'General']);

        Schema::create('vdlp_redirect_redirects', function (Blueprint $table) {
            // Table configuration
            $table->engine = 'InnoDB';

            // Columns
            $table->increments('id');
            $table->unsignedInteger('category_id')->nullable();
            $table->char('match_type', 12)->nullable();
            $table->char('target_type', 12)->default(Redirect::TARGET_TYPE_PATH_URL);
            $table->char('from_scheme', 5)->default(Redirect::SCHEME_AUTO);
            $table->mediumText('from_url')->nullable();
            $table->char('to_scheme', 5)->default(Redirect::SCHEME_AUTO);
            $table->mediumText('to_url')->nullable();
            $table->mediumText('test_url')->nullable();
            $table->string('cms_page')->nullable();
            $table->string('static_page')->nullable();
            $table->text('requirements')->nullable();
            $table->char('status_code', 3);
            $table->unsignedInteger('hits')->default(0);
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(0);
            $table->boolean('test_lab')->default(0);
            $table->mediumText('test_lab_path')->nullable();
            $table->boolean('system')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('sort_order');
            $table->index('is_enabled');

            // Foreign keys
            $table->foreign('category_id')
                ->references('id')
                ->on('vdlp_redirect_categories')
                ->onDelete('set null');
        });

        Schema::create('vdlp_redirect_clients', function (Blueprint $table) {
            // Table configuration
            $table->engine = 'InnoDB';

            // Columns
            $table->increments('id');
            $table->unsignedInteger('redirect_id');
            $table->timestamp('timestamp')->nullable();
            $table->unsignedTinyInteger('day');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('crawler')->nullable();

            // Indexes
            $table->index(['redirect_id', 'day', 'month', 'year'], 'redirect_dmy');
            $table->index(['redirect_id', 'month', 'year'], 'redirect_my');

            // Foreign keys
            $table->foreign('redirect_id', 'vdlp_redirect_client')
                ->references('id')
                ->on('vdlp_redirect_redirects')
                ->onDelete('cascade');
        });

        Schema::create('vdlp_redirect_redirect_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('redirect_id');
            $table->mediumText('from_url');
            $table->mediumText('to_url');
            $table->char('status_code', 3);
            $table->unsignedTinyInteger('day');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->dateTime('date_time');

            $table->index(['redirect_id', 'day', 'month', 'year'], 'redirect_log_dmy');
            $table->index(['redirect_id', 'month', 'year'], 'redirect_log_my');

            $table->foreign('redirect_id', 'vdlp_redirect_log')
                ->references('id')
                ->on('vdlp_redirect_redirects')
                ->onDelete('cascade');
        });

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $settings = Settings::instance();
        $settings->logging_enabled = '1';
        $settings->statistics_enabled = '1';
        $settings->test_lab_enabled = '1';
        $settings->save();

        try {
            PublishManager::instance()->publish();
        } catch (Exception $e) {
            // ..
        }
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('vdlp_redirect_categories');
        Schema::dropIfExists('vdlp_redirect_clients');
        Schema::dropIfExists('vdlp_redirect_redirect_logs');
        Schema::dropIfExists('vdlp_redirect_redirects');

        Schema::enableForeignKeyConstraints();
    }
}
