<?php

namespace Butler\Service\Database;

use Butler\Service\Database\HostParser;
use Illuminate\Database\Connectors\ConnectionFactory as BaseConnectionFactory;

class ConnectionFactory extends BaseConnectionFactory
{
    protected function getWriteConfig(array $config)
    {
        $writeConfig = parent::getWriteConfig($config);

        if ($writeConfig['use_first_available_host'] ?? true) {
            $writeConfig['host'] = $this->parseHosts($writeConfig)[0];
        }

        return $writeConfig;
    }

    protected function parseHosts(array $config)
    {
        return (new HostParser())
            ->maintenance($config['maintenance'] ?? [])
            ->parse(parent::parseHosts($config));
    }
}
