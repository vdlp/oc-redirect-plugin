<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Psr\Log\LoggerInterface;
use Schema;
use Throwable;

class AddMonthYearCrawlerIndexOnClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('vdlp_redirect_clients', static function (Blueprint $table): void {
            $table->index(
                [
                    'month',
                    'year',
                    'crawler',
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
        try {
            Schema::table('vdlp_redirect_clients', static function (Blueprint $table): void {
                $table->dropIndex('month_year_crawler');
                $table->dropIndex('month_year');
            });
        } catch (Throwable $e) {
            /** @var LoggerInterface $logger */
            $logger = resolve(LoggerInterface::class);
            $logger->error(sprintf(
                'Vdlp.Redirect: Unable to drop index `%s`, `%s` from table `%s`: %s',
                'month_year_crawler',
                'month_year',
                'vdlp_redirect_clients',
                $e->getMessage()
            ));
        }
    }
}
