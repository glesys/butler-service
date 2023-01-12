<?php

namespace Butler\Service\Tests\Feature;

use Butler\Health\Repository;
use Butler\Service\Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_it_returns_json()
    {
        $this->mock(Repository::class, function ($mock) {
            $mock->expects('__invoke')->andReturns(['foo' => 'bar']);
        });

        $this->getJson(route('health'))
            ->assertOk()
            ->assertExactJson(['foo' => 'bar']);
    }
}
