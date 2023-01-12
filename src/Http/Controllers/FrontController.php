<?php

namespace Butler\Service\Http\Controllers;

use Butler\Health\Repository as HealthRepository;
use Butler\Service\Repositories\DatabaseRepository;

class FrontController extends Controller
{
    public function __invoke(HealthRepository $healthRepository)
    {
        $data = $healthRepository();
        $data['databaseConnections'] = app(DatabaseRepository::class)();

        return view('service::front', $data);
    }
}
