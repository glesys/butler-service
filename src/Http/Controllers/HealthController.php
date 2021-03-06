<?php

namespace Butler\Service\Http\Controllers;

use Butler\Service\Repositories\HealthRepository;

class HealthController extends Controller
{
    public function __invoke(HealthRepository $healthRepository)
    {
        return $healthRepository();
    }
}
