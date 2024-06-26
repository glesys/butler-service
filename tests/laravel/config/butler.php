<?php

return [

    'health' => [

        'checks' => [
            App\TestCheck::class,
        ],

    ],

    'service' => [

        'extra' => [
            'config' => [
                'app.timezone' => 'Europe/Stockholm',
                'foo' => 'bar',
            ],
        ],

    ],

    'custom' => [

        'foo' => 'bar',

    ],

    'sso' => [

        'enabled' => true,
        'fake' => true,
        'url' => 'http://localhost',
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'redirect' => '/auth/callback',

    ],

];
