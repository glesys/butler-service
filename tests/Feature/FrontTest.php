<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;

class FrontTest extends TestCase
{
    public function test_happy_path()
    {
        $this->get(route('front'))
            ->assertOk()
            ->assertSee(config('app.name'))
            ->assertSee('type Query {');
    }
}
