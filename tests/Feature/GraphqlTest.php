<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;
use Illuminate\Auth\GenericUser;

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
        $this->actingAs(new GenericUser(['id' => 1]))
            ->postJson(route('graphql'), ['query' => '{ __schema { directives { name } } }'])
            ->assertOk()
            ->assertJsonPath('data.__schema.directives.*.name', [
                'skip',
                'include',
                'deprecated',
            ]);
    }
}
