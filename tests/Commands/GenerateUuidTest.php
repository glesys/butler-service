<?php

namespace Butler\Service\Tests\Commands;

use Butler\Service\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;

class GenerateUuidTest extends TestCase
{
    private function runCommand(?array $parameters = []): int
    {
        return $this->app[Kernel::class]->call('generate:uuid', $parameters);
    }

    public function test_command()
    {
        $this->assertEquals(0, $this->runCommand());
        $this->assertRegExp('/^[a-f0-9-]{36}$/', $this->app[Kernel::class]->output());
    }

    public function test_command_with_strip_dashes_option()
    {
        $this->assertEquals(0, $this->runCommand(['--strip-dashes' => true]));
        $this->assertRegExp('/^[a-f0-9]{32}$/', $this->app[Kernel::class]->output());
    }
}
