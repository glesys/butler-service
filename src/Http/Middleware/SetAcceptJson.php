<?php

declare(strict_types=1);

namespace Butler\Service\Http\Middleware;

use Closure;

class SetAcceptJson
{
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
