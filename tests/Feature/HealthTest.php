<?php

namespace Butler\Service\Tests\Feature;

use Butler\Health\Repository;
use Butler\Service\Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_it_can_return_view()
    {
        $this->withoutVite()
            ->get(route('health'))
            ->assertOk()
            ->assertHeader('cache-control', 'no-store, private')
            ->assertViewIs('butler::health.index');
    }

    public function test_it_can_returns_json()
    {
        $this->mock(Repository::class, function ($mock) {
            $mock->expects('__invoke')->andReturns(['foo' => 'bar']);
        });

        $this->getJson(route('health'))
            ->assertOk()
            ->assertExactJson(['foo' => 'bar']);
    }
}
