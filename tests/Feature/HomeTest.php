<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;

class HomeTest extends TestCase
{
    public function test_happy_path()
    {
        $this->withoutVite()
            ->get(route('home'))
            ->assertOk()
            ->assertViewIs('butler::home');
    }
}
