<?php

namespace Butler\Service\Tests\Bus;

use Butler\Service\Jobs\QueueableJob;

class JobWithCorrelationId extends QueueableJob
{
    public function handle()
    {
        return true;
    }
}
