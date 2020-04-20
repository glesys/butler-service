<?php

return [

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

        'graylog' => env('BUTLER_SERVICE_GRAYLOG', false),
        'horizon' => env('BUTLER_SERVICE_HORIZON', false),

        'routes' => [
            'readme' => '/',
            'schema' => '/schema',
            'graphql' => '/graphql',
        ],

        'extra' => [
            'config' => [],
            'aliases' => [],
            'providers' => [],
        ],

    ],

];
