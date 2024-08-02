<?php

namespace Butler\Service\Http\Controllers;

use Butler\Health\Repository;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Controllers\HasMiddleware;

class HealthController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            SetCacheHeaders::using('no_store'),
        ];
    }

    public function __invoke(Repository $repository)
    {
        return request()->wantsJson()
            ? $repository()
            : view('butler::health.index');
    }
}
