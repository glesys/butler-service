<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Testing;

use Butler\Service\Testing\Concerns\InteractsWithGraphql;
use Butler\Service\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;

class InteractsWithGraphqlTest extends TestCase
{
    #[DataProvider('assertGraphqlSentHappyProvider')]
    public function test_assertGraphqlSent_happy_paths(...$arguments)
    {
        Http::fake()->post('localhost/graphql', [
            'query' => '{ query }',
            'variables' => ['foo' => 'bar'],
        ]);

        $class = $this->dummyClass();
        $result = $class->assertGraphqlSent(...$arguments);

        $this->assertSame(1, $this->getCount());
        $this->assertSame($class, $result);
    }

    public static function assertGraphqlSentHappyProvider()
    {
        return [
            [],
            [fn (Request $request) => $request->url() === 'localhost/graphql'],
            [fn () => true, '{ query }'],
            [fn () => true, null, ['foo' => 'bar']],
            [fn () => true, '{ query }', ['foo' => 'bar']],
            [null, '{ query }'],
            [null, null, ['foo' => 'bar']],
        ];
    }

    #[DataProvider('assertGraphqlSentSadProvider')]
    public function test_assertGraphqlSent_sad_paths(...$arguments)
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('An expected GraphQL request was not recorded.');

        Http::fake()->post('localhost/graphql', [
            'query' => '{ query }',
            'variables' => ['foo' => 'bar'],
        ]);

        $this->dummyClass()->assertGraphqlSent(...$arguments);
    }

    public static function assertGraphqlSentSadProvider()
    {
        return [
            [fn (Request $request) => $request->url() === 'localhost/fooql'],
            [fn () => false, '{ query }'],
            [fn () => false, '{ query }', ['foo' => 'bar']],
            [fn () => false, null, ['foo' => 'bar']],

            [fn () => true, '{ quer }'],
            [fn () => true, null, ['foo' => 'baz']],

            [null, '{ quer }'],
            [null, '{ query }', ['foo' => 'baz']],

            [null, null, ['foo' => 'baz']],
        ];
    }

    public function test_assertGraphqlSent_checks_http_method()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('An expected GraphQL request was not recorded.');

        Http::fake()->get('localhost/graphql', ['query' => '{ query }']);

        $this->dummyClass()->assertGraphqlSent();
    }

    public function test_assertGraphqlSent_checks_if_valid_graphql()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('An expected GraphQL request was not recorded.');

        Http::fake()->post('localhost/graphql', ['query' => '{ bad { query }']);

        $this->dummyClass()->assertGraphqlSent();
    }

    private function dummyClass(): object
    {
        return new class
        {
            use InteractsWithGraphql;

            public function fail(string $message): void
            {
                throw new AssertionFailedError($message);
            }
        };
    }
}
