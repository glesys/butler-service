<?php

namespace Butler\Service\Tests\Listeners;

use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Butler\Service\Listeners\FlushBugsnag;
use Butler\Service\Tests\TestCase;
use Mockery;

class FlushBugsnagTest extends TestCase
{
    public function test_flush_bugsnag()
    {
        $event = tap(Mockery::mock(), fn ($mock) => $mock->app = app());

        $this->mock('bugsnag', function ($mock) {
            $mock->expects()->flush();
            $mock->expects()->clearBreadcrumbs();
        });

        $this->mock(Tracker::class, fn ($mock) => $mock->expects()->clear());

        (new FlushBugsnag())->handle($event);
    }
}
