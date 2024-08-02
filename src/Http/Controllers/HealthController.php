<?php

declare(strict_types=1);

namespace Butler\Service\Http\Controllers;

use Butler\Health\Repository;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class HealthController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            SetCacheHeaders::using('no_store'),
        ];
    }

    public function __invoke(Request $request, Repository $repository)
    {
        return $request->wantsJson()
            ? $repository()
            : view('butler::health.index');
    }
}
