<?php

return [

    'audit' => [

        'url' => 'http://localhost',

        'token' => 'secret',

        'driver' => 'log',

    ],

    'auth' => [

        'secret_key' => '',

        'required_claims' => [
            'aud' => 'example-service',
            'iss' => 'example-service',
        ],

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

    'service' => [

        'routes' => [
            'front' => '/',
            'graphql' => '/graphql',
            'health' => '/health',
        ],

        'health' => [
            'checks' => [
                Butler\Service\Tests\TestCheck::class
            ],
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
