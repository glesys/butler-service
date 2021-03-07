<?php

namespace Butler\Service\Console\Commands;

use Butler\Service\Models\Consumer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

# TODO rename GenerateConsumer?
class CreateConsumer extends Command
{
    protected $signature = 'create-consumer {name} {token?}';

    protected $description = 'Create a new consumer for this service.';

    public function handle()
    {
        $consumer = Consumer::create([
            'name' => $this->argument('name'),
            'api_token' => $this->argument('token') ?? Str::uuid(),
        ]);

        $this->output->success(
            "Consumer '{$consumer->name}' created with api_token '{$consumer->api_token}'"
        );
    }
}
