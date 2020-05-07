<?php

return [

    'audit' => [

        'url' => env('BUTLER_AUDIT_URL'),

        'token' => env('BUTLER_AUDIT_TOKEN'),

        'driver' => env('BUTLER_AUDIT_DRIVER'),

    ],

    'auth' => [

        'secret_key' => env('BUTLER_AUTH_SECRET_KEY', ''),

        'required_claims' => [
            'aud' => 'butler-service',
            'iss' => 'butler-service',
        ],

    ],

    'guru' => [

        'driver' => env('BUTLER_GURU_DRIVER', 'file'),

        'events' => [
            // 'example.event' => [
            //     EventHandler::class,
            // ],
        ],

    ],

    'graphql' => [

        'include_debug_message' => env('BUTLER_GRAPHQL_INCLUDE_DEBUG_MESSAGE', false),
        'include_trace' => env('BUTLER_GRAPHQL_INCLUDE_TRACE', false),

        'namespace' => env('BUTLER_GRAPHQL_NAMESPACE', '\\App\\Http\\Graphql\\'),

        'schema' => env('BUTLER_GRAPHQL_SCHEMA', app_path('Http/Graphql/schema.graphql')),

    ],

    'service' => [

        'routes' => [
            'front' => '/',
            'graphql' => '/graphql',
            'health' => '/health',
        ],

        'health' => [
            'checks' => [],
        ],

        'extra' => [
            'config' => [],
            'aliases' => [],
            'providers' => [],
        ],

    ],

];
