<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Updates;

use Illuminate\Database\DatabaseManager;
use October\Rain\Database\Updates\Migration;

class ChangeColumnTypesFromCharToVarChar extends Migration
{
    public function up(): void
    {
        /** @var DatabaseManager $database */
        $database = resolve('db');

        if ($database->getDriverName() === 'pgsql') {
            $database->statement(implode(' ', [
                'ALTER TABLE vdlp_redirect_redirects',
                'ALTER COLUMN match_type TYPE VARCHAR(12),',
                'ALTER COLUMN target_type TYPE VARCHAR(12),',
                'ALTER COLUMN from_scheme TYPE VARCHAR(5),',
                'ALTER COLUMN to_scheme TYPE VARCHAR(5),',
                'ALTER COLUMN status_code TYPE VARCHAR(3);',
            ]));

            $database->statement(implode(' ', [
                'ALTER TABLE vdlp_redirect_redirects',
                "ALTER COLUMN target_type SET DEFAULT 'path_or_url',",
                "ALTER COLUMN from_scheme SET DEFAULT 'auto',",
                "ALTER COLUMN to_scheme SET DEFAULT 'auto';"
            ]));

            $database->statement(implode(' ', [
                'ALTER TABLE vdlp_redirect_redirect_logs',
                'ALTER COLUMN status_code TYPE VARCHAR(3);',
            ]));
        }

        if ($database->getDriverName() === 'mysql') {
            $database->statement('ALTER TABLE `vdlp_redirect_redirects` CHANGE `match_type` `match_type` VARCHAR(12) NULL DEFAULT NULL;');
            $database->statement("ALTER TABLE `vdlp_redirect_redirects` CHANGE `target_type` `target_type` VARCHAR(12) NOT NULL DEFAULT 'path_or_url';");
            $database->statement("ALTER TABLE `vdlp_redirect_redirects` CHANGE `from_scheme` `from_scheme` VARCHAR(5) NOT NULL DEFAULT 'auto';");
            $database->statement("ALTER TABLE `vdlp_redirect_redirects` CHANGE `to_scheme` `to_scheme` VARCHAR(5) NOT NULL DEFAULT 'auto';");
            $database->statement("ALTER TABLE `vdlp_redirect_redirects` CHANGE `status_code` `status_code` VARCHAR(3) NOT NULL DEFAULT '';");
            $database->statement("ALTER TABLE `vdlp_redirect_redirect_logs` CHANGE `status_code` `status_code` VARCHAR(3) NOT NULL DEFAULT '';");
        }

        // 'sqlite' does not support the char type, so it doesn't need to be altered.
    }

    public function down(): void
    {
    }
}
