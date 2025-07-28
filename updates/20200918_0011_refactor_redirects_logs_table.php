<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class RefactorRedirectsLogTable extends Migration
{
    public function up(): void
    {
        try {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('vdlp_redirect_redirect_logs');
            Schema::enableForeignKeyConstraints();
        } catch (Throwable $throwable) {
            echo 'Migration error: ' . $throwable->getMessage(), PHP_EOL;
            echo 'Database table `vdlp_redirect_redirect_logs` could not be removed.', PHP_EOL;
            echo 'Please remove it manually and try running the database migrations again.', PHP_EOL;

            return;
        }

        Schema::create('vdlp_redirect_redirect_logs', static function (Blueprint $table): void {
            // Table MySQL configuration
            $table->engine = 'InnoDB';

            // Columns
            $table->increments('id');
            $table->unsignedInteger('redirect_id');
            $table->string('from_to_hash', 40);
            $table->string('status_code', 3);
            $table->mediumText('from_url');
            $table->mediumText('to_url');
            $table->unsignedInteger('hits')
                ->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('redirect_id', 'vdlp_redirect_log')
                ->references('id')
                ->on('vdlp_redirect_redirects')
                ->onDelete('cascade');

            // Indexes
            $table->unique([
                'redirect_id',
                'from_to_hash',
                'status_code',
            ], 'redirect_log_unique');
        });
    }

    public function down(): void
    {
        try {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('vdlp_redirect_redirect_logs');
            Schema::enableForeignKeyConstraints();
        } catch (Throwable $throwable) {
            echo 'Migration error: ' . $throwable->getMessage(), PHP_EOL;
            echo 'Database table `vdlp_redirect_redirect_logs` could not be removed.', PHP_EOL;
            echo 'Please remove it manually and try running the database migrations again.', PHP_EOL;

            return;
        }

        Schema::create('vdlp_redirect_redirect_logs', static function (Blueprint $table): void {
            // Table MySQL configuration
            $table->engine = 'InnoDB';

            // Columns
            $table->increments('id');
            $table->unsignedInteger('redirect_id');
            $table->mediumText('from_url');
            $table->mediumText('to_url');
            $table->string('status_code', 3);
            $table->unsignedTinyInteger('day');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->dateTime('date_time');

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
}
