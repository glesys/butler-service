<?php

namespace Butler\Service\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AboutController extends Controller
{
    public function __invoke()
    {
        return view('butler::about', [
            'about' => $this->about(),
            'databaseConnections' => $this->databaseConnections(),
        ]);
    }

    private function about(): array
    {
        Artisan::call('about', ['--json' => true]);

        $json = str(Artisan::output())->trim();

        return json_decode($json, true);
    }

    private function databaseConnections(): array
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
