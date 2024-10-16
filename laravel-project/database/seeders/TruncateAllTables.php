<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateAllTables extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->getTargetTableNames() as $tableName) {
            $this->command->getOutput()->writeln("<comment>Truncating:</comment> {$tableName}");
            DB::table($tableName)->truncate();
            $this->command->getOutput()->writeln("<info>Truncated:</info> {$tableName}");
        }

        Schema::enableForeignKeyConstraints();
    }

    private function getTargetTableNames(): array
    {
        $excludes = ['migrations'];

        return array_diff($this->getAllTableNames(), $excludes);
    }

    private function getAllTableNames(): array
    {
        return DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }
}
