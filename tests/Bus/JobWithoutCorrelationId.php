<?php

namespace Butler\Service\Tests\Bus;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class JobWithoutCorrelationId implements ShouldQueue
{
    use Queueable;

    public function handle()
    {
        return true;
    }
}
