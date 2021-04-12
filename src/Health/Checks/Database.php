<?php

namespace Butler\Service\Health\Checks;

use Butler\Service\Health\Check;
use Butler\Service\Health\Result;
use Illuminate\Support\Facades\DB;

class Database extends Check
{
    public string $group = 'core';
    public string $description = 'Check all database connections.';

    public function run(): Result
    {
        $connected = $checked = 0;
        $connectionKeys = collect(config('database.connections'))->keys();

        if ($connectionKeys->isEmpty()) {
            return Result::unknown('No database connections found.');
        }

        foreach ($connectionKeys->all() as $connection) {
            ++$checked;

            try {
                if (DB::connection($connection)->getPdo()) {
                    ++$connected;
                }
            } catch (\Exception) {
                //
            }
        }

        if ($connected === $checked) {
            $result = Result::ok('Connected to all databases.');
        } else {
            $result = Result::critical("Connected to {$connected} of {$checked} databases.");
        }

        return tap($result)->value($connected / $checked);
    }
}
