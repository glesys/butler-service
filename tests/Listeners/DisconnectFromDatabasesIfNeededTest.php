<?php

namespace Butler\Service\Tests\Listeners;

use Butler\Service\Database\DisconnectFromDatabasesInMaintenance;
use Butler\Service\Listeners\DisconnectFromDatabasesIfNeeded;
use Butler\Service\Tests\TestCase;
use Laravel\Octane\Events\TickTerminated;

class DisconnectFromDatabasesIfNeededTest extends TestCase
{
    public function test_handle()
    {
        $event = new TickTerminated(app(), app());

        $this->mock(DisconnectFromDatabasesInMaintenance::class, function ($mock) {
            $mock->expects()->__invoke(app('db'));
        });

        (new DisconnectFromDatabasesIfNeeded())->handle($event);
    }
}
