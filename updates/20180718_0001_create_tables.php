<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use October\Rain\Database\Updates\Migration;
use Psr\Log\LoggerInterface;
use Throwable;
use Vdlp\Redirect\Models\Category;
use Vdlp\Redirect\Models\Redirect;
use Vdlp\Redirect\Models\Settings;

class CreateTables extends Migration
{
    public function up(): void
    {
        /** @var DatabaseManager $database */
        $database = resolve('db');

        $schema = $database->getSchemaBuilder();

        // Drop any existing index keys on SQLite databases #24.
        if (
            $schema->hasTable('adrenth_redirect_redirects')
            && $database->getDriverName() === 'sqlite'
        ) {
            $statements = [
                'DROP INDEX redirect_dmy;',
                'DROP INDEX redirect_my;',
                'DROP INDEX redirect_log_dmy;',
                'DROP INDEX redirect_log_my;',
                'DROP INDEX month_year;',
            ];

            foreach ($statements as $statement) {
                try {
                    $database->statement($statement);
                } catch (Throwable $e) {
                    /** @var LoggerInterface $logger */
                    $logger = resolve(LoggerInterface::class);
                    $logger->error(sprintf('Vdlp.Redirect: Unable to drop index: %s'. $e->getMessage()));

                    continue;
                }
            }
        }

        if (!Schema::hasTable('vdlp_redirect_categories')) {
            Schema::create('vdlp_redirect_categories', static function (Blueprint $table): void {
                // Table configuration
                $table->engine = 'InnoDB';

                // Columns
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });

            Category::create(['name' => 'General']);
        }

        if (!Schema::hasTable('vdlp_redirect_redirects')) {
            Schema::create('vdlp_redirect_redirects', static function (Blueprint $table): void {
                    // Table MySQL configuration
                    $table->engine = 'InnoDB';
                    // Columns
                    $table->increments('id');
                    $table->unsignedInteger('category_id')->nullable();
                    // @see 20200408_0008_change_column_types_from_char_to_varchar.php
                    $table->char('match_type', 12)->nullable();
                    // @see 20200408_0008_change_column_types_from_char_to_varchar.php
                    $table->char('target_type', 12)
                        ->default(Redirect::TARGET_TYPE_PATH_URL);
                    // @see 20200408_0008_change_column_types_from_char_to_varchar.php
                    $table->char('from_scheme', 5)
                        ->default(Redirect::SCHEME_AUTO);
                    $table->mediumText('from_url')->nullable();
                    $table->char('to_scheme', 5)
                        ->default(Redirect::SCHEME_AUTO);
                    $table->mediumText('to_url')->nullable();
                    $table->mediumText('test_url')->nullable();
                    $table->string('cms_page')->nullable();
                    $table->string('static_page')->nullable();
                    $table->text('requirements')->nullable();
                    // @see 20200408_0008_change_column_types_from_char_to_varchar.php
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
        }

        if (!Schema::hasTable('vdlp_redirect_clients')) {
            Schema::create('vdlp_redirect_clients', static function (Blueprint $table): void {
                // Table MySQL configuration
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
        }

        if (!Schema::hasTable('vdlp_redirect_redirect_logs')) {
            Schema::create('vdlp_redirect_redirect_logs', static function (Blueprint $table): void {
                // Table MySQL configuration
                $table->engine = 'InnoDB';
                // Columns
                $table->increments('id');
                $table->unsignedInteger('redirect_id');
                $table->mediumText('from_url');
                $table->mediumText('to_url');
                // @see 20200408_0008_change_column_types_from_char_to_varchar.php
                $table->char('status_code', 3);
                $table->unsignedTinyInteger('day');
                $table->unsignedTinyInteger('month');
                $table->unsignedSmallInteger('year');
                $table->dateTime('date_time');
                $table->timestamps();
                // Indexes
                $table->index(['redirect_id', 'day', 'month', 'year'], 'redirect_log_dmy');
                $table->index(['redirect_id', 'month', 'year'], 'redirect_log_my');
                // Foreign keys
                $table->foreign('redirect_id', 'vdlp_redirect_log')
                    ->references('id')
                    ->on('vdlp_redirect_redirects')
                    ->onDelete('cascade');
            });
        }

        try {
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $settings = Settings::instance();
            $settings->logging_enabled = '1';
            $settings->statistics_enabled = '1';
            $settings->test_lab_enabled = '1';
            $settings->save();
        } catch (Throwable $e) {
            /** @var LoggerInterface $logger */
            $logger = resolve(LoggerInterface::class);
            $logger->error(sprintf(
                'Vdlp.Redirect: Unable to save default settings: %s',
                $e->getMessage()
            ));
        }
    }

    public function down(): void
    {
        try {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('vdlp_redirect_clients');
            Schema::dropIfExists('vdlp_redirect_redirect_logs');
            Schema::dropIfExists('vdlp_redirect_redirects');
            Schema::dropIfExists('vdlp_redirect_categories');
            Schema::enableForeignKeyConstraints();
        } catch (Throwable $e) {
            /** @var LoggerInterface $logger */
            $logger = resolve(LoggerInterface::class);
            $logger->error(sprintf(
                'Vdlp.Redirect: Unable to drop all tables: %s',
                $e->getMessage()
            ));
        }
    }
}
