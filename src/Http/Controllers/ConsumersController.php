<?php

namespace Butler\Service\Http\Controllers;

use Butler\Service\Models\Consumer;

class ConsumersController extends Controller
{
    public function __invoke()
    {
        return Consumer::select('name')->get();
    }
}
