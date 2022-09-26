<?php

namespace Butler\Service\Tests\Middleware;

use Butler\Service\Http\Middleware\SetAcceptJson;
use Butler\Service\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SetAcceptJsonTest extends TestCase
{
    public function test_middleware()
    {
        $request = Request::create('/');

        $this->assertNotEquals('application/json', $request->headers->get('Accept'));

        (new SetAcceptJson())->handle($request, fn () => new Response('data', 204));

        $this->assertEquals('application/json', $request->headers->get('Accept'));
    }
}
