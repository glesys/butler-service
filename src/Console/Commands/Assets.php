<?php

declare(strict_types=1);

namespace Butler\Service\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Assets extends Command
{
    protected $signature = 'butler-service:assets';

    protected $description = 'Publish butler-service assets';

    public function handle()
    {
        File::deleteDirectory(public_path('vendor/butler'));

        $this->call('vendor:publish', ['--tag' => 'butler-assets']);
    }
}
