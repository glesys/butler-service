<?php

declare(strict_types=1);

namespace Butler\Service\Http\Controllers;

use Illuminate\Support\Facades\File;

class HomeController
{
    public function __invoke()
    {
        return view('butler::home', [
            'graphql' => File::get(config('butler.graphql.schema')),
        ]);
    }
}
