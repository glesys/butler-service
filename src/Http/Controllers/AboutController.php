<?php

declare(strict_types=1);

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
        return collect(config('database.connections'))->map(fn ($connection, $key) => [
            'driver' => $connection['driver'],
            'host' => $connection['host'] ?? '',
            'port' => $connection['port'] ?? '',
            'charset' => $connection['charset'] ?? '',
            'collation' => $connection['collation'] ?? '',
            'connected' => rescue(fn () => DB::connection($key)->getPdo() ? true : false, report: false),
        ])->toArray();
    }
}
