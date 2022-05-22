<?php

namespace Butler\Service\Tests\Repositories;

use Butler\Service\Repositories\DatabaseRepository;
use Butler\Service\Tests\TestCase;

class DatabaseRepositoryTest extends TestCase
{
    public function test_connection_with_host_option()
    {
        $this->travelTo('02:00');

        config(['database.connections' => [
            'default' => [
                'driver' => 'driver',
                'host' => ['host1', 'host2', 'host3'],
                'maintenance' => ['* 1 * * *', '* 2 * * *', '* 3 * * *'],
            ],
        ]]);

        $this->assertEquals([
            'default' => [
                'driver' => 'driver',
                'charset' => null,
                'collation' => null,
                'hosts' => [
                    ['address' => 'host1', 'available' => true, 'maintenance' => '* 1 * * *', 'type' => 'rw'],
                    ['address' => 'host2', 'available' => false, 'maintenance' => '* 2 * * *', 'type' => 'rw'],
                    ['address' => 'host3', 'available' => true, 'maintenance' => '* 3 * * *', 'type' => 'rw'],
                ],
            ],
        ], (new DatabaseRepository())->__invoke());
    }

    public function test_connection_with_read_and_write_option()
    {
        $this->travelTo('03:00');

        config(['database.connections' => [
            'default' => [
                'driver' => 'driver',
                'read' => [
                    'host' => ['host1', 'host2', 'host3', 'host4'],
                    'maintenance' => ['* 1 * * *', '* 2 * * *', '* 3 * * *', '* 4 * * *'],
                ],
                'write' => [
                    'host' => ['host3', 'host4', 'host5'],
                    'maintenance' => ['* 3 * * *', '* 4 * * *', '* 5 * * *'],
                ],
            ],
        ]]);

        $this->assertEquals([
            'default' => [
                'driver' => 'driver',
                'charset' => null,
                'collation' => null,
                'hosts' => [
                    ['address' => 'host1', 'available' => true, 'maintenance' => '* 1 * * *', 'type' => 'r'],
                    ['address' => 'host2', 'available' => true, 'maintenance' => '* 2 * * *', 'type' => 'r'],
                    ['address' => 'host3', 'available' => false, 'maintenance' => '* 3 * * *', 'type' => 'rw'],
                    ['address' => 'host4', 'available' => true, 'maintenance' => '* 4 * * *', 'type' => 'rW'],
                    ['address' => 'host5', 'available' => true, 'maintenance' => '* 5 * * *', 'type' => 'w'],
                ],
            ],
        ], (new DatabaseRepository())->__invoke());
    }
}
