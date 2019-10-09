<?php

namespace Butler\Service\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateUuid extends Command
{
    protected $signature = 'generate:uuid {--strip-dashes}';

    protected $description = 'Generate an uuid';

    public function handle()
    {
        $uuid = Str::uuid();

        if ($this->option('strip-dashes')) {
            $uuid = $uuid->getHex();
        }

        $this->output->writeln($uuid);
    }
}
