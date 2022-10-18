<?php

declare(strict_types=1);

namespace Butler\Service\Repositories;

use Butler\Service\Database\HostParser;
use Illuminate\Support\Arr;

class DatabaseRepository
{
    public function __invoke(): array
    {
        return collect(config('database.connections'))->mapWithKeys(function ($config, $name) {
            $configuredHosts = Arr::wrap($config['host'] ?? []);

            $availableHosts = (new HostParser())
                ->maintenance($config['maintenance'] ?? [])
                ->parse($configuredHosts);

            return [
                $name => [
                    'driver' => $config['driver'],
                    'charset' => $config['charset'] ?? null,
                    'collation' => $config['collation'] ?? null,
                    'hosts' => collect($configuredHosts)->map(fn ($host, $index) => [
                        'address' => $host,
                        'available' => in_array($host, $availableHosts),
                        'maintenance' => $config['maintenance'][$index] ?? null,
                    ])->toArray(),
                ],
            ];
        })->toArray();
    }
}
