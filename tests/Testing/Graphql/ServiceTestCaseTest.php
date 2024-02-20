<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Testing\Graphql;

use Butler\Service\Graphql\Service;
use Butler\Service\Testing\Graphql\Query;
use Butler\Service\Testing\Graphql\ServiceTestCase;
use Butler\Service\Tests\TestCase;
use Illuminate\Support\Arr;

class ServiceTestCaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.dummy-service' => [
            'url' => 'http://localhost/graphql',
            'token' => 'secret',
        ]]);
    }

    public function test_service()
    {
        $this->assertInstanceOf(DummyService::class, $this->dummyTest()->service());
    }

    public function test_guessServiceClass()
    {
        $this->assertEquals('App\ServiceTestCase', $this->dummyTest()->guessServiceClass());
    }

    private function dummyTest(): object
    {
        return new class('dummyTest') extends ServiceTestCase
        {
            protected ?string $serviceClass = DummyService::class;

            public static function queryProvider()
            {
                return [
                    'normalQuery' => [
                        Query::test(fn () => (new DummyService())->normalQuery())
                            ->sends('{ foo }')
                            ->receives(Arr::undot(['data.bar' => 'baz']))
                            ->returns('baz'),
                    ],
                    'variables' => [
                        Query::test(fn () => (new DummyService())->queryWithVariables())
                            ->sends('{ foo }', ['bar' => 'baz'])
                            ->receives(Arr::undot(['data.foo' => 'baz']))
                            ->returns('baz'),
                    ],
                    'normalCollect' => [
                        Query::test(fn () => (new DummyService())->normalCollect())
                            ->sends('{ foobars }')
                            ->receives(Arr::undot(['data.foobars' => ['foo1', 'foo2']]))
                            ->returns(fn ($result) => $result->toArray() === ['foo1', 'foo2']),
                    ],
                    'normalRequest' => [
                        Query::test(fn () => (new DummyService())->normalRequest())
                            ->sends('{ foo }')
                            ->receives(Arr::undot(['data.foo' => 'baz']))
                            ->returns(['foo' => 'baz'])
                            ->throwsExceptionOnErrors(),
                    ],
                ];
            }
        };
    }
}

class DummyService extends Service
{
    public function normalQuery(): mixed
    {
        return $this->query('{ foo { bar } }', key: 'bar');
    }

    public function queryWithVariables(): mixed
    {
        return $this->query('{ foo { bar } }', ['bar' => 'baz'], 'bar');
    }

    public function normalCollect(): mixed
    {
        return $this->collect('{ foobars }');
    }

    public function normalRequest(): mixed
    {
        return $this->request('{ foo }');
    }
}
