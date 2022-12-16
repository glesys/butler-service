<?php

namespace Butler\Service\Tests;

use Butler\Service\Jobs\Contracts\Viewable;
use Butler\Service\Jobs\QueueableJob;

class TestViewableJob extends QueueableJob implements Viewable
{
    public function handle()
    {
        //
    }

    public function viewData(): array
    {
        return [
            'foo' => 'bar',
        ];
    }
}
