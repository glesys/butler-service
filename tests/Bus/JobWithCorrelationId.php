<?php

namespace Butler\Service\Tests\Bus;

use Butler\Service\Bus\WithCorrelationId;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class JobWithCorrelationId implements ShouldQueue
{
    use Queueable;
    use WithCorrelationId;

    public function handle()
    {
        return true;
    }
}
