<?php

namespace Butler\Service\Tests\Graphql;

use Butler\Service\Graphql\Service;
use Butler\Service\Tests\TestCase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;
use RuntimeException;

class ServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services' => [
                'dummy-service' => [
                    'url' => 'http://localhost/graphql',
                    'token' => 'secret',
                ],
            ],
        ]);

        Http::preventStrayRequests();
    }

    public function test_request_send_request_correctly()
    {
        Http::fakeSequence()->push(['data' => 'things']);

        $result = (new DummyService())->request('{ things }', ['foo' => 'bar']);

        $this->assertEquals('things', $result);

        Http::assertSent(fn ($request)
            => $request->method() === 'POST'
            && $request->url() === 'http://localhost/graphql'
            && $request->hasHeader('Authorization', 'Bearer secret')
            && $request->hasHeader('X-Correlation-ID')
            && str($request->header('X-Correlation-ID')[0])->isUuid()
            && $request->data() === [
                'query' => '{ things }',
                'variables' => [
                    'foo' => 'bar',
                ],
            ]
        );
    }

    public function test_request_throw_exception_on_server_error()
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('HTTP request returned status code 500');

        Http::fakeSequence()->push('server error', 500);

        (new DummyService())->request('{}');
    }

    public function test_request_throw_exception_when_data_was_not_returned()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('{"not data":"foobar"}');

        Http::fakeSequence()->push(['not data' => 'foobar']);

        (new DummyService())->request('{}');
    }

    public function test_query_send_request_correctly()
    {
        Http::fakeSequence()->push(['data' => 'data']);

        (new DummyService())->query('{ foobar }', ['foo' => 'bar']);

        Http::assertSent(fn ($request) => $request->data() === [
            'query' => '{ foobar }',
            'variables' => [
                'foo' => 'bar',
            ],
        ]);
    }

    public function test_query_with_key_argument()
    {
        Http::fake(fn () => Http::response([
            'data' => [
                'things' => [
                    'foo' => 'foz',
                    'bar' => 'baz',
                ],
            ],
        ]));

        $this->assertNull((new DummyService())->query('{ things }', key: 'things.404'));

        $this->assertEquals(
            'foz',
            (new DummyService())->query('{ things }', key: 'things.foo')
        );

        $this->assertEquals(
            [
                'things' => [
                    'foo' => 'foz',
                    'bar' => 'baz',
                ],
            ],
            (new DummyService())->query('{ things }')
        );
    }

    public function test_query_with_default_argument()
    {
        Http::fakeSequence()->push(['data' => ['foo' => 'bar']]);

        $this->assertEquals(
            'default',
            (new DummyService())->query('{ things }', key: 'foz', default: 'default'),
        );
    }

    public function test_query_with_rescue_log_exception_and_return_default_on_http_error()
    {
        Http::fakeSequence()->push(['server error'], 500);

        Log::shouldReceive('error')->with(
            Mockery::on(fn ($error) => str_contains($error, 'HTTP request returned status code 500')),
            Mockery::on(fn ($array)
                => $array['exception'] instanceof RequestException
                && $array['variables'] === ['foobar']
            )
        );

        $result = (new DummyService())->query('{ foobar }', ['foobar'], default: 'default');

        $this->assertEquals('default', $result);
    }

    public function test_query_without_rescue_throw_exception()
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('HTTP request returned status code 500');

        Http::fakeSequence()->push('server error', 500);

        Log::shouldReceive('error')->never();

        (new DummyService())->query('{ foobar }', rescue: false);
    }

    public function test_collect()
    {
        Http::fake(fn () => Http::response([
            'data' => [
                'things' => [
                    ['name' => 'thing1'],
                    ['name' => 'thing2'],
                ],
            ],
        ]));

        $this->assertInstanceOf(Collection::class, (new DummyService())->collect('{ things }'));

        $this->assertEquals(
            [
                ['name' => 'thing1'],
                ['name' => 'thing2'],
            ],
            (new DummyService())->collect('{ things }')->all()
        );

        $this->assertEquals(
            [
                ['name' => 'thing1'],
                ['name' => 'thing2'],
            ],
            (new DummyService())->collect('{ things }', key: 'things')->all(),
        );

        $this->assertEquals(
            ['name' => 'thing2'],
            (new DummyService())->collect('{ things }', key: 'things.1')->all(),
        );
    }

    public function test_configKey()
    {
        $this->assertEquals(
            'services.dummy-service',
            (new DummyService())->configKey()
        );
    }
}

class DummyService extends Service
{
}
