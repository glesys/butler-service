<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Graphql;

use Butler\Service\Graphql\Client;
use Butler\Service\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ClientTest extends TestCase
{
    public function test_fromArray()
    {
        $arrayWithoutTimeout = [
            'url' => 'localhost',
            'token' => 'secret',
        ];

        $arrayWithTimeout = [
            'url' => 'localhost',
            'token' => 'secret',
            'timeout' => 3,
        ];

        $this->assertInstanceOf(Client::class, Client::fromArray($arrayWithoutTimeout));
        $this->assertInstanceOf(Client::class, Client::fromArray($arrayWithTimeout));
    }

    public function test_fromConfig()
    {
        config(['services.example-service' => [
            'url' => 'localhost',
            'token' => 'secret',
        ]]);

        $this->assertInstanceOf(Client::class, Client::fromConfig('services.example-service'));
    }

    public function test_request_happy_path()
    {
        Http::fakeSequence()->push(['data' => 'foobar']);

        $result = (new Client('url', 't0ken'))
            ->request('query {}', ['foo' => 'bar']);

        $this->assertEquals(['data' => 'foobar'], $result);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'url'
                && $request->hasHeader('Authorization', 'Bearer t0ken')
                && $request->hasHeader('X-Correlation-ID')
                && str($request->header('X-Correlation-ID')[0])->isUuid();
        });
    }

    public function test_request_throws_exception_on_server_error()
    {
        $this->expectException(\Illuminate\Http\Client\RequestException::class);
        $this->expectExceptionMessage('HTTP request returned status code 500');

        Http::fakeSequence()->push('error', 500);

        (new Client('url', 't0ken'))->request('query {}');
    }

    public function test_request_throws_exception_when_data_was_not_returned()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('{"not-data":"foobar"}');

        Http::fakeSequence()->push(['not-data' => 'foobar']);

        (new Client('url', 't0ken'))->request('query {}');
    }
}
