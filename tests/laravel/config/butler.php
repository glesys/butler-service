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
            'aliases' => [
                'Foobar' => Illuminate\Support\Facades\Cache::class,
            ],
            'providers' => [
                App\Providers\ExtraServiceProvider::class,
            ],
        ],

    ],

    'custom' => [

        'foo' => 'bar',

    ],

    'sso' => [

        'enabled' => true,
        'url' => 'http://localhost',
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'redirect' => '/auth/callback',

    ],

];
