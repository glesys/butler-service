<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Models\Consumer;
use Butler\Service\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ConsumersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication()
    {
        $this->getJson(route('consumers'))->assertStatus(401);
    }

    public function test_it_requires_authorization()
    {
        Sanctum::actingAs(new Consumer(), ['some-ability']);

        $this->getJson(route('consumers'))->assertForbidden();
    }

    public function test_it_returns_json()
    {
        Consumer::create(['name' => 'consumer-1']);

        Sanctum::actingAs(new Consumer(), ['*']);

        $this->getJson(route('consumers'))
            ->assertOk()
            ->assertExactJson([
                ['name' => 'consumer-1'],
            ]);
    }
}
