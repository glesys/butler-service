<?php

namespace Butler\Service\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    protected function proxies()
    {
        return config('trustedproxy.proxies');
    }
}
