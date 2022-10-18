<?php

declare(strict_types=1);

namespace Butler\Service\Console\Commands;

use Illuminate\Console\Command;

class GenerateUuid extends Command
{
    protected $signature = 'generate:uuid {--strip-dashes}';

    protected $description = 'Generate an uuid';

    public function handle()
    {
        $uuid = str()->uuid();

        if ($this->option('strip-dashes')) {
            $uuid = $uuid->getHex();
        }

        $this->output->writeln($uuid->toString());
    }
}
