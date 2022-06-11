<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Psr\Log\LoggerInterface;
use Schema;
use Throwable;

class AddRedirectTimestampCrawlerIndexOnClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('vdlp_redirect_clients', static function (Blueprint $table): void {
            $table->index(
                [
                    'redirect_id',
                    'timestamp',
                    'crawler',
                ],
                'redirect_timestamp_crawler'
            );
        });
    }

    public function down(): void
    {
        try {
            Schema::table('vdlp_redirect_clients', static function (Blueprint $table): void {
                $table->dropIndex('redirect_timestamp_crawler');
            });
        } catch (Throwable $e) {
            /** @var LoggerInterface $logger */
            $logger = resolve(LoggerInterface::class);
            $logger->error(sprintf(
                'Vdlp.Redirect: Unable to drop index `%s` from table `%s`: %s',
                'redirect_timestamp_crawler',
                'vdlp_redirect_clients',
                $e->getMessage()
            ));
        }
    }
}
