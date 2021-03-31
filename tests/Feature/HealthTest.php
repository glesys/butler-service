<?php

// phpcs:disable PSR2.Methods.FunctionCallSignature.SpaceBeforeCloseBracket

namespace Butler\Service\Tests\Feature;

use Butler\Service\Models\Consumer;
use Butler\Service\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class HealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_json()
    {
        $this->getJson(route('health'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('service', fn (AssertableJson $json) => $json
                    ->hasAll('php', 'laravel', 'butlerService', 'timezone')
                    ->where('name', config('app.name'))
                )
                ->has('checks', 4, fn (AssertableJson $json) => $json
                    ->hasAll('name', 'slug', 'group', 'description')
                    ->has('result', fn (AssertableJson $json) => $json
                        ->hasAll('message', 'state', 'value')
                    )
                )
            );
    }

    public function test_it_includes_consumers_when_authorized()
    {
        Sanctum::actingAs(new Consumer(), ['view-consumers']);

        Consumer::create(['name' => 'consumer-1']);

        $this->getJson(route('health'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->hasAll('service', 'checks')
                ->has('consumers', 1, fn (AssertableJson $json) => $json
                    ->where('name', 'consumer-1')
                )
            );
    }
}
