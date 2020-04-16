<?php

return [

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

        'bugsnag' => false,
        'graylog' => false,
        'horizon' => false,

        'routes' => [
            'readme' => '/',
            'schema' => '/schema',
            'graphql' => '/graphql',
        ],

        'extra' => [
            'config' => [
                'foo' => 'bar',
            ],
            'aliases' => [
                'Foobar' => Illuminate\Support\Facades\Cache::class,
            ],
            'providers' => [
                Butler\Service\Tests\FoobarServiceProvider::class,
            ],
        ],

    ],

];
