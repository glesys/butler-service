<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Models\Consumer;
use Butler\Service\Tests\TestCase;

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
            ->assertJsonPath('data.__schema.directives.*.name', [
                'skip',
                'include',
                'deprecated',
            ]);
    }
}
