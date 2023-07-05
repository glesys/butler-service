<?php

return [

    'audit' => [

        'url' => env('BUTLER_AUDIT_URL'),

        'token' => env('BUTLER_AUDIT_TOKEN'),

        'driver' => env('BUTLER_AUDIT_DRIVER', 'log'),

    ],

    'graphql' => [

        'include_debug_message' => env('BUTLER_GRAPHQL_INCLUDE_DEBUG_MESSAGE', false),
        'include_trace' => env('BUTLER_GRAPHQL_INCLUDE_TRACE', false),

        'namespace' => env('BUTLER_GRAPHQL_NAMESPACE', 'App\\Http\\Graphql\\'),

        'schema' => env('BUTLER_GRAPHQL_SCHEMA', app_path('Http/Graphql/schema.graphql')),

    ],

    'health' => [

        'checks' => [],

    ],

    'service' => [

        'extra' => [
            'config' => [],
            'aliases' => [],
            'providers' => [],
        ],

    ],

    'sso' => [

        'enabled' => env('BUTLER_SSO_ENABLED', false),
        'fake' => env('BUTLER_SSO_FAKE', false),
        'url' => env('BUTLER_SSO_URL'),
        'client_id' => env('BUTLER_SSO_CLIENT_ID'),
        'client_secret' => env('BUTLER_SSO_CLIENT_SECRET'),
        'redirect' => env('BUTLER_SSO_REDIRECT', '/auth/callback'),

    ],

];
