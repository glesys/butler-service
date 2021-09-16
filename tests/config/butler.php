<?php

return [

    'audit' => [

        'url' => 'http://localhost',

        'token' => 'secret',

        'driver' => 'log',

    ],

    'guru' => [

        'driver' => env('BUTLER_GURU_DRIVER', 'file'),

        'events' => [],

    ],

    'graphql' => [

        'include_debug_message' => false,
        'include_trace' => false,

        'namespace' => '\\App\\Http\\Graphql\\',

        'schema' => app_path('Http/Graphql/schema.graphql'),

    ],

    'health' => [

        'route' => false,

        'checks' => [
            Butler\Service\Tests\TestCheck::class,
        ],

    ],

    'service' => [

        'routes' => [
            'front' => '/',
            'graphql' => '/graphql',
            'health' => '/health',
        ],

        'extra' => [
            'config' => [
                'app.timezone' => 'Europe/Stockholm',
                'foo' => 'bar',
            ],
            'aliases' => [
                'Foobar' => Illuminate\Support\Facades\Cache::class,
            ],
            'providers' => [
                Butler\Service\Tests\ExtraServiceProvider::class,
            ],
        ],

    ],

];
