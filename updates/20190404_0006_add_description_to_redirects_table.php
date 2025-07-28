<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Psr\Log\LoggerInterface;
use Schema;
use Throwable;

class AddDescriptionToRedirectsTable extends Migration
{
    public function up(): void
    {
        Schema::table('vdlp_redirect_redirects', static function (Blueprint $table): void {
            $table->string('description')
                ->nullable()
                ->after('system');
        });
    }

    public function down(): void
    {
        try {
            Schema::table('vdlp_redirect_redirects', static function (Blueprint $table): void {
                $table->dropColumn('description');
            });
        } catch (Throwable $e) {
            /** @var LoggerInterface $logger */
            $logger = resolve(LoggerInterface::class);
            $logger->error(sprintf(
                'Vdlp.Redirect: Unable to drop index `%s` from table `%s`: %s',
                'description',
                'vdlp_redirect_redirects',
                $e->getMessage()
            ));
        }
    }
}
