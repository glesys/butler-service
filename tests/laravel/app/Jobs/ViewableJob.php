<?php

declare(strict_types=1);

namespace App\Jobs;

use Butler\Service\Jobs\Contracts\Viewable;
use Butler\Service\Jobs\QueueableJob;

class ViewableJob extends QueueableJob implements Viewable
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
