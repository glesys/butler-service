<?php

return [

    'audit' => [

        'url' => env('BUTLER_AUDIT_URL'),

        'token' => env('BUTLER_AUDIT_TOKEN'),

        'driver' => env('BUTLER_AUDIT_DRIVER', 'log'),

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

    'health' => [

        'checks' => [],

    ],

    'service' => [

        'routes' => [
            'front' => '/',
            'graphql' => '/graphql',
            'health' => '/health',
        ],

        'extra' => [
            'config' => [],
            'aliases' => [],
            'providers' => [],
        ],

    ],

];
