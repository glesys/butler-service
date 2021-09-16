<?php

namespace Butler\Service\Http\Controllers;

use Butler\Health\Repository as HealthRepository;

class FrontController extends Controller
{
    public function __invoke(HealthRepository $healthRepository)
    {
        return view('service::front', $healthRepository());
    }
}
