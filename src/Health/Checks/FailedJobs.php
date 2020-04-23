<?php

namespace Butler\Service\Health\Checks;

use Butler\Service\Health\Check;
use Butler\Service\Health\Result;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FailedJobs extends Check
{
    public string $group = 'core';
    public string $name = 'Failed jobs';
    public string $description = 'Check if there are failed jobs.';

    public function run(): Result
    {
        $connection = config('queue.failed.database');
        $tableName = config('queue.failed.table');

        try {
            throw_unless(Schema::connection($connection)->hasTable($tableName), Exception::class);
        } catch (Exception $_) {
            return Result::unknown("Table {$tableName} not found.");
        }

        if ($count = DB::connection($connection)->table($tableName)->count()) {
            return tap(Result::critical("{$count} failed jobs."))->value($count);
        }

        return tap(Result::ok('No failed jobs.'))->value(0);
    }
}
