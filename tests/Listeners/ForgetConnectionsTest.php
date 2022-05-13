<?php

namespace Butler\Service\Tests\Listeners;

use Butler\Service\Database\DatabaseManager;
use Butler\Service\Listeners\ForgetConnections;
use Butler\Service\Tests\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Application;
use Laravel\Octane\Events\RequestTerminated;
use Mockery;
use PDO;

class ForgetConnectionsTest extends TestCase
{
    public function test_handle()
    {
        config([
            'database.connections' => [
                'db1' => [
                    'host' => 'host1',
                ],
                'db2' => [
                    'host' => ['host1', 'host2'],
                ],
                'db3' => [
                    'host' => ['host1', 'host2'],
                    'options' => [PDO::ATTR_PERSISTENT => true],
                ],
                'db4' => [
                    'host' => ['host1', 'host2'],
                    'maintenance' => ['* * * * *'],
                ],
            ],
        ]);

        $databaseManager = Mockery::mock(DatabaseManager::class, function ($mock) {
            $mock->expects()->getConnections()->andReturns([
                'db1' => Mockery::mock(Connection::class),
                'db2' => Mockery::mock(Connection::class),
                'db3' => Mockery::mock(Connection::class),
                'db4' => Mockery::mock(Connection::class, function ($mock) {
                    $mock->expects()->disconnect();
                }),
            ]);

            $mock->expects()->forget('db3');
        });

        $sandbox = Mockery::mock(Application::class, function ($mock) use ($databaseManager) {
            $mock->expects()->make('db')->andReturns($databaseManager);
        });

        $event = Mockery::mock(RequestTerminated::class, function ($mock) use ($sandbox) {
            $mock->app = app();
            $mock->sandbox = $sandbox;
        });

        (new ForgetConnections())->handle($event);
    }
}
