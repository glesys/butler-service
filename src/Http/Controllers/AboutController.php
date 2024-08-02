<?php

namespace Butler\Service\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AboutController
{
    public function __invoke()
    {
        return view('butler::about', [
            'databaseConnections' => $this->databaseConnections(),
        ]);
    }

    protected function databaseConnections(): array
    {
        return collect(config('database.connections'))->map(function ($connection, $key) {
            return [
                'driver' => $connection['driver'],
                'host' => $connection['host'] ?? '',
                'port' => $connection['port'] ?? '',
                'charset' => $connection['charset'] ?? '',
                'collation' => $connection['collation'] ?? '',
                'connected' => rescue(fn () => DB::connection($key)->getPdo() ? true : false, report: false),
            ];
        })->toArray();
    }
}
