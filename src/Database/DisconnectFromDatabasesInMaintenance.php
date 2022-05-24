<?php

namespace Butler\Service\Database;

use Cron\CronExpression;
use Illuminate\Database\DatabaseManager;
use PDO;

class DisconnectFromDatabasesInMaintenance
{
    public function __invoke(DatabaseManager $databaseManager): void
    {
        foreach ($databaseManager->getConnections() as $name => $connection) {
            $connectionConfig = config("database.connections.{$name}", []);

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
        $hosts = $config['host'] ?? $config['read']['host'] ?? null;

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

        $cronExpressions = array_merge(
            $config['maintenance'] ?? [],
            $config['read']['maintenance'] ?? [],
            $config['write']['maintenance'] ?? [],
        );

        foreach ($cronExpressions as $cronExpression) {
            if ((new CronExpression($cronExpression))->isDue($now)) {
                return true;
            }
        }

        return false;
    }
}
