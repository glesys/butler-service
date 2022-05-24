<?php

namespace Butler\Service\Tests\Database;

use Butler\Service\Database\DatabaseManager;
use Butler\Service\Database\DisconnectFromDatabasesInMaintenance;
use Butler\Service\Tests\TestCase;
use Illuminate\Database\Connection;
use Mockery;
use PDO;

class DisconnectFromDatabasesInMaintenanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultConnection = Mockery::mock(Connection::class);
        $this->readWriteConnection = Mockery::mock(Connection::class);

        $this->databaseManager = Mockery::mock(DatabaseManager::class, function ($mock) {
            $mock->expects()->getConnections()->andReturns([
                'default' => $this->defaultConnection,
                'readwrite' => $this->readWriteConnection,
            ])->byDefault();
        });
    }

    public function test_single_host_is_ignored()
    {
        config(['database.connections.default.host' => 'host1']);
        config(['database.connections.readwrite.read.host' => 'host1']);

        (new DisconnectFromDatabasesInMaintenance())($this->databaseManager);
    }

    public function test_hosts_without_maintenance_is_ignored()
    {
        config(['database.connections.default.host' => ['host1', 'host2']]);
        config(['database.connections.readwrite.read.host' => ['host1', 'host2']]);

        (new DisconnectFromDatabasesInMaintenance())($this->databaseManager);
    }

    public function test_hosts_with_not_ongoing_maintenance_is_ignored()
    {
        $this->travelTo('03:00');

        config(['database.connections.default' => [
            'host' => ['host1', 'host2'],
            'maintenance' => ['* 1 * * *', '* 2 * * *'],
        ]]);

        config(['database.connections.readwrite.read' => [
            'host' => ['host1', 'host2'],
            'maintenance' => ['* 1 * * *', '* 2 * * *'],
        ]]);

        (new DisconnectFromDatabasesInMaintenance())($this->databaseManager);
    }

    public function test_hosts_with_ongoing_maintenance_is_disconnected()
    {
        $this->travelTo('02:00');

        config(['database.connections.default' => [
            'host' => ['host1', 'host2'],
            'maintenance' => ['* 1 * * *', '* 2 * * *'],
        ]]);

        config(['database.connections.readwrite.read' => [
            'host' => ['host1', 'host2'],
            'maintenance' => ['* 1 * * *', '* 2 * * *'],
        ]]);

        $this->defaultConnection->expects()->disconnect();
        $this->readWriteConnection->expects()->disconnect();

        (new DisconnectFromDatabasesInMaintenance())($this->databaseManager);
    }

    public function test_hosts_with_persistent_connection_option_is_forgotten()
    {
        config(['database.connections.default' => [
            'host' => ['host1', 'host2'],
            'options' => [PDO::ATTR_PERSISTENT => true],
        ]]);

        config(['database.connections.readwrite' => [
            'read' => ['host' => ['host1', 'host2']],
            'options' => [PDO::ATTR_PERSISTENT => true],
        ]]);

        $this->databaseManager->expects()->forget('default');
        $this->databaseManager->expects()->forget('readwrite');

        (new DisconnectFromDatabasesInMaintenance())($this->databaseManager);
    }
}
