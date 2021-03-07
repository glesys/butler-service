<?php

// phpcs:disable PSR2.Methods.FunctionCallSignature.SpaceBeforeCloseBracket

namespace Butler\Service\Tests\Feature;

use Butler\Service\Models\Consumer;
use Butler\Service\Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class GraphqlTest extends TestCase
{
    public function test_unauthenticated()
    {
        $this->postJson(route('graphql'))
            ->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_authenticated()
    {
        $this->actingAs(new Consumer())
            ->postJson(route('graphql'), ['query' => '{ __schema { directives { name } } }'])
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->whereAll([
                'data.__schema.directives.0.name' => 'skip',
                'data.__schema.directives.1.name' => 'include',
                'data.__schema.directives.2.name' => 'deprecated',
            ]));
    }
}
