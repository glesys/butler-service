<?php

// phpcs:disable PSR2.Methods.FunctionCallSignature.SpaceBeforeCloseBracket

namespace Butler\Service\Tests\Feature;

use Butler\Service\Models\Consumer;
use Butler\Service\Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class GraphqlTest extends TestCase
{
    public function test_unauthenticated()
    {
        $this->graphql('{ ping }')->assertUnauthorized()->assertExactJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function test_query_for_consumer_without_query_ability_is_forbidden()
    {
        Sanctum::actingAs(new Consumer(), []);

        $this->graphql('{ ping }')->assertForbidden()->assertExactJson([
            'message' => 'This action is unauthorized.',
        ]);
    }

    public function test_query_for_consumer_with_query_ability_is_allowed()
    {
        Sanctum::actingAs(new Consumer(), ['query']);

        $this->graphql('{ ping }')->assertOk()->assertJsonPath('data.ping', 'pong');
    }

    public function test_query_for_consumer_with_all_abilities_is_allowed()
    {
        Sanctum::actingAs(new Consumer(), ['*']);

        $this->graphql('{ ping }')->assertOk()->assertJsonPath('data.ping', 'pong');
    }

    public function test_mutation_for_consumer_without_mutation_ability_is_forbidden()
    {
        Sanctum::actingAs(new Consumer(), ['query']);

        $this->graphql('mutation { start }')->assertForbidden()->assertExactJson([
            'message' => 'This action is unauthorized.',
        ]);
    }

    public function test_mutation_for_consumer_with_mutation_ability_is_allowed()
    {
        Sanctum::actingAs(new Consumer(), ['mutation']);

        $this->graphql('mutation { start }')
            ->assertOk()
            ->assertJsonPath('data.start', 'started');
    }

    public function test_mutation_for_consumer_with_all_abilities_is_allowed()
    {
        Sanctum::actingAs(new Consumer(), ['*']);

        $this->graphql('mutation { start }')
            ->assertOk()
            ->assertJsonPath('data.start', 'started');
    }

    public function test_query_without_operation_is_not_allowed()
    {
        Sanctum::actingAs(new Consumer());

        $this->graphql('fragment Foo on __Bar { baz }')
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Invalid operation.']);
    }

    private function graphql($query)
    {
        return $this->postJson(route('graphql'), compact('query'));
    }
}
