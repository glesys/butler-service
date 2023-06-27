<?php

namespace Butler\Service\Http\Controllers;

use Butler\Health\Repository;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Controller as BaseController;

class HealthController extends BaseController
{
    public function __construct()
    {
        $this->middleware(SetCacheHeaders::using('no_store'));
    }

    public function __invoke(Repository $repository)
    {
        return request()->wantsJson()
            ? $repository()
            : view('butler::health.index');
    }
}
