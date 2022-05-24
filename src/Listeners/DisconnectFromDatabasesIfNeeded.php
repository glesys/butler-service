<?php

namespace Butler\Service\Listeners;

use Butler\Service\Database\DisconnectFromDatabasesInMaintenance;

class DisconnectFromDatabasesIfNeeded
{
    public function handle($event): void
    {
        $event->app->make(DisconnectFromDatabasesInMaintenance::class)->__invoke(
            $event->sandbox->make('db'),
        );
    }
}
