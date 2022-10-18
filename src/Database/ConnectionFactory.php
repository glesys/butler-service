<?php

declare(strict_types=1);

namespace Butler\Service\Database;

use Illuminate\Database\Connectors\ConnectionFactory as BaseConnectionFactory;

class ConnectionFactory extends BaseConnectionFactory
{
    protected function parseHosts(array $config)
    {
        return (new HostParser())
            ->maintenance($config['maintenance'] ?? [])
            ->parse(parent::parseHosts($config));
    }
}
