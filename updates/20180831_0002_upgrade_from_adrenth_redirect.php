<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use Exception;
use Illuminate\Database\DatabaseManager;
use October\Rain\Database\Updates\Migration;
use Psr\Log\LoggerInterface;
use Throwable;

class UpgradeFromAdrenthRedirect extends Migration
{
    /**
     * @throws Exception
     */
    public function up(): void
    {
        /** @var DatabaseManager $database */
        $database = resolve('db');

        /** @var LoggerInterface $log */
        $log = resolve(LoggerInterface::class);

        $schema = $database->getSchemaBuilder();

        if (!$schema->hasTable('adrenth_redirect_redirects')) {
            // Skip upgrade migration.
            $log->info('No upgrade of Vdlp.Redirect needed. Fresh installation.');

            return;
        }

        try {
            $database->transaction(function () use ($database): void {
                $this->disableForeignKeyCheck($database);

                $mapping = [
                    'adrenth_redirect_categories' => 'vdlp_redirect_categories',
                    'adrenth_redirect_redirects' => 'vdlp_redirect_redirects',
                    'adrenth_redirect_redirect_logs' => 'vdlp_redirect_redirect_logs',
                    'adrenth_redirect_clients' => 'vdlp_redirect_clients',
                ];

                // language=ignore
                foreach ($mapping as $from => $to) {
                    // Make sure newly created tables are empty.
                    $database->table($to)->delete();

                    // Move data from old tables to new ones.
                    $database->statement("INSERT INTO `$to` SELECT * FROM `$from`;");
                }

                // Migrate plugin settings.
                $database->table('system_settings')
                    ->where('item', '=', 'vdlp_redirect_settings')
                    ->delete();

                // language=ignore
                $database->statement(
                    'INSERT INTO `system_settings` '
                    . "SELECT NULL, 'vdlp_redirect_settings', `value` "
                    . 'FROM `system_settings` '
                    . "WHERE `item` = 'adrenth_redirect_settings';"
                );

                $this->enableForeignKeyCheck($database);
            });
        } catch (Throwable $e) {
            $log->error(sprintf(
                'Vdlp.Redirect: Could not upgrade plugin Vdlp.Redirect from Adrenth.Redirect: %s',
                $e->getMessage()
            ));
        }
    }

    public function down(): void
    {
        // No migrations to reverse.
    }

    private function disableForeignKeyCheck(DatabaseManager $database): void
    {
        if ($database->getDriverName() === 'sqlite') {
            $database->raw('PRAGMA foreign_keys = OFF;');
        }

        if ($database->getDriverName() === 'mysql') {
            $database->raw('SET FOREIGN_KEY_CHECKS = 0;');
        }

        if ($database->getDriverName() === 'pgsql') {
            $database->raw('SET CONSTRAINTS ALL DEFERRED;');
        }
    }

    private function enableForeignKeyCheck(DatabaseManager $database): void
    {
        if ($database->getDriverName() === 'sqlite') {
            $database->raw('PRAGMA foreign_keys = ON;');
        }

        if ($database->getDriverName() === 'mysql') {
            $database->raw('SET FOREIGN_KEY_CHECKS = 1;');
        }

        if ($database->getDriverName() === 'pgsql') {
            $database->raw('PRAGMA foreign_keys = ON;');
        }
    }
}
