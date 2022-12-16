<?php

namespace Butler\Service\Http\Controllers;

use Butler\Health\Repository;
use Illuminate\Routing\Controller as BaseController;

class HealthController extends BaseController
{
    public function __construct()
    {
        $this->middleware('cache.headers:no_store,no_cache,must_revalidate');
    }

    public function __invoke(Repository $repository)
    {
        return request()->wantsJson()
            ? $repository()
            : view('butler::health.index');
    }
}
