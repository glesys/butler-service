<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;

class ReadmeTest extends TestCase
{
    public function test_happy_path()
    {
        $this->get(route('readme'))
            ->assertOk()
            ->assertSee(config('app.name'));
    }
}
