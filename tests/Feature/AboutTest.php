<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;

class AboutTest extends TestCase
{
    public function test_happy_path()
    {
        $this->withoutVite()
            ->get(route('about'))
            ->assertOk()
            ->assertViewIs('butler::about');
    }
}
