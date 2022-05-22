<?php

namespace Butler\Service\Repositories;

use Butler\Service\Database\HostParser;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DatabaseRepository
{
    public function __invoke(): array
    {
        return collect(config('database.connections'))->mapWithKeys(function ($config, $name) {
            $configuredHosts = isset($config['read'])
                ? $this->configuredReadWriteHosts($config)
                : $this->configuredHosts($config);

            return [
                $name => [
                    'driver' => $config['driver'],
                    'charset' => $config['charset'] ?? null,
                    'collation' => $config['collation'] ?? null,
                    'hosts' => $configuredHosts->toArray(),
                ],
            ];
        })->toArray();
    }

    private function configuredHosts(array $config): Collection
    {
        $availableHosts = $this->availableHosts($config);

        return collect($config['host'] ?? [])->map(fn ($host, $index) => [
            'address' => $host,
            'available' => in_array($host, $availableHosts),
            'maintenance' => $config['maintenance'][$index] ?? null,
            'type' => 'rw',
        ]);
    }

    private function configuredReadWriteHosts(array $config): Collection
    {
        $readConfig = Arr::except(array_merge($config, $config['read']), ['read', 'write']);
        $writeConfig = Arr::except(array_merge($config, $config['write']), ['read', 'write']);

        $availableReadHosts = $this->availableHosts($readConfig);
        $availableWriteHosts = $this->availableHosts($writeConfig);

        $configuredHosts = collect();

        foreach ($readConfig['host'] as $index => $host) {
            $configuredHosts->put($host, [
                'address' => $host,
                'available' => in_array($host, $availableReadHosts),
                'maintenance' => $readConfig['maintenance'][$index] ?? null,
                'type' => in_array($host, $writeConfig['host']) ? 'rw' : 'r',
            ]);
        }

        foreach ($writeConfig['host'] as $index => $host) {
            if (! $configuredHosts->has($host)) {
                $configuredHosts->put($host, [
                    'address' => $host,
                    'available' => in_array($host, $availableWriteHosts),
                    'maintenance' => $writeConfig['maintenance'][$index] ?? null,
                    'type' => 'w',
                ]);
            }
        }

        if ($writeConfig['use_first_available_host'] ?? true) {
            $firstAvailableWriteHost = $availableWriteHosts[0] ?? null;

            if ($configuredHosts->has($firstAvailableWriteHost)) {
                $writeHost = $configuredHosts->get($firstAvailableWriteHost);
                $writeHost['type'] = str_replace('w', 'W', $writeHost['type']);
                $configuredHosts->put($firstAvailableWriteHost, $writeHost);
            }
        }

        return $configuredHosts->values();
    }

    private function availableHosts(array $config): array
    {
        return (new HostParser())
            ->maintenance($config['maintenance'] ?? [])
            ->parse(Arr::wrap($config['host'] ?? []));
    }
}
