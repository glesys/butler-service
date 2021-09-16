<?php

namespace Butler\Service\Listeners;

use Bugsnag\BugsnagLaravel\Queue\Tracker;

class FlushBugsnag
{
    public function handle($event)
    {
        $event->app->bugsnag->flush();
        $event->app->bugsnag->clearBreadcrumbs();
        $event->app->make(Tracker::class)->clear();
    }
}
