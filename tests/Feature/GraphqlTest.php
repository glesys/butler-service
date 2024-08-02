<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Feature;

use Butler\Service\Testing\Concerns\InteractsWithAuthentication;
use Butler\Service\Tests\TestCase;

class GraphqlTest extends TestCase
{
    use InteractsWithAuthentication;

    public function test_as_guest()
    {
        $this->post(route('graphql'))->assertUnauthorized();

        $this->graphql('{ ping }')->assertUnauthorized()->assertExactJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_query_for_consumer_without_query_ability_is_forbidden()
    {
        $this->actingAsConsumer(abilities: [])
            ->graphql('{ ping }')
            ->assertForbidden()
            ->assertExactJson([
                'message' => 'This action is unauthorized.',
            ]);
    }

    public function test_query_for_consumer_with_query_ability_is_allowed()
    {
        $this->actingAsConsumer(abilities: ['query'])
            ->graphql('{ ping }')
            ->assertOk()
            ->assertJsonPath('data.ping', 'pong');
    }

    public function test_query_for_consumer_with_all_abilities_is_allowed()
    {
        $this->actingAsConsumer(abilities: ['*'])
            ->graphql('{ ping }')
            ->assertOk()
            ->assertJsonPath('data.ping', 'pong');
    }

    public function test_mutation_for_consumer_without_mutation_ability_is_forbidden()
    {
        $this->actingAsConsumer(abilities: ['query'])
            ->graphql('mutation { start }')
            ->assertForbidden()
            ->assertExactJson([
                'message' => 'This action is unauthorized.',
            ]);
    }

    public function test_mutation_for_consumer_with_mutation_ability_is_allowed()
    {
        $this->actingAsConsumer(abilities: ['mutation'])
            ->graphql('mutation { start }')
            ->assertOk()
            ->assertJsonPath('data.start', 'started');
    }

    public function test_mutation_for_consumer_with_all_abilities_is_allowed()
    {
        $this->actingAsConsumer(abilities: ['*'])
            ->graphql('mutation { start }')
            ->assertOk()
            ->assertJsonPath('data.start', 'started');
    }

    public function test_query_without_operation_is_not_allowed()
    {
        $this->actingAsConsumer()
            ->graphql('fragment Foo on __Bar { baz }')
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Invalid operation.']);
    }

    private function graphql($query)
    {
        return $this->postJson(route('graphql'), compact('query'));
    }
}
