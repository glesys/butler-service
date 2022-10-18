<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Repositories;

use Butler\Service\Repositories\DatabaseRepository;
use Butler\Service\Tests\TestCase;

class DatabaseRepositoryTest extends TestCase
{
    public function test_connections()
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
                    ['address' => 'host1', 'available' => true, 'maintenance' => '* 1 * * *'],
                    ['address' => 'host2', 'available' => false, 'maintenance' => '* 2 * * *'],
                    ['address' => 'host3', 'available' => true, 'maintenance' => '* 3 * * *'],
                ],
            ],
        ], (new DatabaseRepository())->__invoke());
    }
}
