<?php

namespace Butler\Service\Listeners;

use Cron\CronExpression;
use PDO;

class ForgetConnections
{
    public function handle($event): void
    {
        $databaseManager = $event->sandbox->make('db');
        $config = $event->app->make('config');

        foreach ($databaseManager->getConnections() as $name => $connection) {
            $connectionConfig = $config->get("database.connections.$name");

            if (! $this->connectionHasMultipleHosts($connectionConfig)) {
                continue;
            }

            if ($this->connectionIsPersistent($connectionConfig)) {
                $databaseManager->forget($name);
                continue;
            }

            if ($this->connectionHasHostInMaintenance($connectionConfig)) {
                $connection->disconnect();
            }
        }
    }

    private function connectionHasMultipleHosts(array $config): bool
    {
        $hosts = $config['host'] ?? null;

        return is_array($hosts) && count($hosts) > 1;
    }

    private function connectionIsPersistent(array $config): bool
    {
        $options = $config['options'] ?? [];

        return $options[PDO::ATTR_PERSISTENT] ?? false;
    }

    private function connectionHasHostInMaintenance(array $config): bool
    {
        $now = now()->toDateTimeString();

        foreach ($config['maintenance'] ?? [] as $cron) {
            if ((new CronExpression($cron))->isDue($now)) {
                return true;
            }
        }

        return false;
    }
}
