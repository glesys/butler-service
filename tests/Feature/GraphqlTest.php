<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Feature;

use Butler\Service\Testing\Concerns\InteractsWithAuthentication;
use Butler\Service\Tests\TestCase;
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

    private function graphql($query)
    {
        return $this->postJson(route('graphql'), compact('query'));
    }
}
