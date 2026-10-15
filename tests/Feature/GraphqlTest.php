<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Feature;

use Butler\Graphql\Contracts\Scalar;
use Butler\Service\Testing\Concerns\InteractsWithAuthentication;
use Butler\Service\Tests\TestCase;
use GraphQL\Language\AST\ValueNode;
use PHPUnit\Framework\Attributes\DataProvider;

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

    #[DataProvider('queryAbilitiesProvider')]
    public function test_query(bool $expectOk, array $abilities)
    {
        $this->actingAsConsumer(abilities: $abilities)->graphql('{ ping }')->when(
            $expectOk,
            fn ($response) => $response
                ->assertOk()
                ->assertJsonPath('data.ping', 'pong'),
            fn ($response) => $response
                ->assertForbidden()
                ->assertExactJson(['message' => 'This action is unauthorized.']),
        );
    }

    public static function queryAbilitiesProvider()
    {
        return [
            [false, []],
            [false, ['foobar']],
            [false, ['query:pong']],
            [false, ['mutation']],
            [false, ['mutation:stop']],

            [true, ['*']],
            [true, ['query']],
            [true, ['query:ping']],
            [true, ['query:ping', 'query']],
            [true, ['query:ping', 'mutation']],
        ];
    }

    #[DataProvider('mutationAbilitiesProvider')]
    public function test_mutation(bool $expectOk, array $abilities)
    {
        $this->actingAsConsumer(abilities: $abilities)->graphql('mutation { start }')->when(
            $expectOk,
            fn ($response) => $response
                ->assertOk()
                ->assertJsonPath('data.start', 'started'),
            fn ($response) => $response
                ->assertForbidden()
                ->assertExactJson(['message' => 'This action is unauthorized.']),
        );
    }

    public static function mutationAbilitiesProvider()
    {
        return [
            [false, []],
            [false, ['foobar']],
            [false, ['mutation:stop']],
            [false, ['query']],
            [false, ['query:stop']],

            [true, ['*']],
            [true, ['mutation']],
            [true, ['mutation:start']],
            [true, ['mutation:start', 'mutation']],
            [true, ['mutation:start', 'query']],
        ];
    }

    public function test_BackendValidation_exception()
    {
        $this->actingAsConsumer(abilities: ['*'])
            ->graphql('query { backendValidation }')
            ->assertOk()
            ->assertJsonPath('errors.0.message', 'validation-exception')
            ->assertJsonPath('errors.0.extensions.category', 'backend-validation');
    }

    public function test_introspection_is_allowed_without_abilities()
    {
        $this->actingAsConsumer(abilities: [])
            ->graphql(<<<'GQL'
                {
                    __schema {
                        types {
                            ...someTypeFragment
                        }
                    }
                    __type(name: "Query") {
                        name
                    }
                    __typename
                }

                fragment someTypeFragment on __Type {
                    name
                }
                GQL,
            )
            ->assertOk()
            ->assertJsonPath('data.__schema.types.0.name', 'Query')
            ->assertJsonPath('data.__type.name', 'Query')
            ->assertJsonPath('data.__typename', 'Query');
    }

    public function test_long_scalar_literal()
    {
        $this->actingAsConsumer(abilities: ['*'])
            ->graphql('{ long(value: 5368709120) }')
            ->assertOk()
            ->assertExactJson(['data' => ['long' => 5368709120]]);
    }

    public function test_long_scalar_variable()
    {
        $this->actingAsConsumer(abilities: ['*'])
            ->graphql('query ($value: Long!) { long(value: $value) }', ['value' => PHP_INT_MAX])
            ->assertOk()
            ->assertExactJson(['data' => ['long' => PHP_INT_MAX]]);
    }

    public function test_long_scalar_rejects_out_of_range_literal()
    {
        $this->actingAsConsumer(abilities: ['*'])
            ->graphql('{ long(value: 9223372036854775808) }')
            ->assertOk()
            ->assertJsonPath('data', null)
            ->assertJsonPath('errors.0.message', 'Long cannot represent value: 9223372036854775808');
    }

    public function test_long_scalar_rejects_string_variable()
    {
        $this->actingAsConsumer(abilities: ['*'])
            ->graphql('query ($value: Long!) { long(value: $value) }', ['value' => '5368709120'])
            ->assertOk()
            ->assertJsonPath('data', null)
            ->assertJsonPath('errors.0.message', 'Variable "$value" got invalid value "5368709120"; Long cannot represent value: "5368709120"');
    }

    public function test_long_scalar_introspection()
    {
        $this->actingAsConsumer(abilities: [])
            ->graphql('{ __type(name: "Long") { description specifiedByURL } }')
            ->assertOk()
            ->assertExactJson(['data' => ['__type' => [
                'description' => 'Signed 64-bit integer (-2^63 to 2^63-1), serialized as a JSON number.',
                'specifiedByURL' => 'https://scalars.graphql.org/apollographql/long-v0.1.html',
            ]]]);
    }

    public function test_long_scalar_can_be_replaced_by_the_service()
    {
        $this->app->instance('App\\Http\\Graphql\\Scalars\\Long', new class implements Scalar
        {
            public function serialize(mixed $value): mixed
            {
                return "service:{$value}";
            }

            public function parseValue(mixed $value): mixed
            {
                return $value;
            }

            public function parseLiteral(ValueNode $valueNode, ?array $variables = null): mixed
            {
                return $valueNode->value;
            }
        });

        $this->actingAsConsumer(abilities: ['*'])
            ->graphql('{ long(value: 1) }')
            ->assertOk()
            ->assertExactJson(['data' => ['long' => 'service:1']]);
    }

    private function graphql($query, array $variables = [])
    {
        return $this->postJson(route('graphql'), compact('query', 'variables'));
    }
}
