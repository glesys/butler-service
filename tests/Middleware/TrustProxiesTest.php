<?php

namespace Butler\Service\Tests\Middleware;

use Butler\Service\Http\Middleware\TrustProxies;
use Butler\Service\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrustProxiesTest extends TestCase
{
    public function test_middleware()
    {
        config(['trustedproxy.proxies' => '1.2.3.4']);

        $request = Request::create('/');

        (new TrustProxies())->handle($request, fn() => new Response());

        $this->assertEquals(['1.2.3.4'], $request->getTrustedProxies());
    }
}
