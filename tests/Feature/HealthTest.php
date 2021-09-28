<?php

// phpcs:disable PSR2.Methods.FunctionCallSignature.SpaceBeforeCloseBracket

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class HealthTest extends TestCase
{
    public function test_it_returns_json()
    {
        $this->getJson(route('health'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('application', fn (AssertableJson $json) => $json
                    ->hasAll('name', 'timezone', 'php', 'laravel', 'butlerHealth', 'butlerService')
                )
                ->has('checks', 4, fn (AssertableJson $json) => $json
                    ->hasAll('name', 'slug', 'group', 'description')
                    ->has('result', fn (AssertableJson $json) => $json
                        ->hasAll('value', 'message', 'state', 'order')
                    )
                    ->etc()
                )
            );
    }
}
