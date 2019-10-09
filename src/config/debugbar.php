<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Debugbar Settings
     |--------------------------------------------------------------------------
     |
     | Debugbar is enabled by default, when debug is set to true in app.php.
     | You can override the value by setting enable to true or false instead of null.
     |
     | You can provide an array of URI's that must be ignored (eg. 'api/*')
     |
     */

    'enabled' => env('DEBUGBAR_ENABLED', null),

    /*
     |--------------------------------------------------------------------------
     | DataCollectors
     |--------------------------------------------------------------------------
     |
     | Enable/disable DataCollectors
     |
     */

    'collectors' => [
        'phpinfo'         => true,  // Php version
        'memory'          => true,  // Memory usage
        'exceptions'      => true,  // Exception displayer
        'db'              => true,  // Show database (PDO) queries and bindings
        'laravel'         => true, // Laravel version and environment

        'events'          => false, // All events fired
        'log'             => false,  // Logs from Monolog (merged in messages if enabled)
        'messages'        => false,  // Messages
        'time'            => false,  // Time Datalogger
        'views'           => false,  // Views with their data
        'route'           => false,  // Current route information
        'auth'            => false, // Display Laravel authentication status
        'gate'            => false, // Display Laravel Gate checks
        'session'         => false,  // Display session data
        'symfony_request' => false,  // Only one can be enabled..
        'mail'            => false,  // Catch mail messages
        'default_request' => false, // Regular or special Symfony request logger
        'logs'            => false, // Add the latest log messages
        'files'           => false, // Show the included files
        'config'          => false, // Display config settings
        'cache'           => false, // Display cache events
    ],

    /*
     |--------------------------------------------------------------------------
     | Extra options
     |--------------------------------------------------------------------------
     |
     | Configure some DataCollectors
     |
     */
    'options' => [
        'db' => [
            'with_params'       => true,   // Render SQL with the parameters substituted
            'backtrace'         => false,   // Use a backtrace to find the origin of the query in your files.
            'timeline'          => false,  // Add the queries to the timeline
            'explain' => [                 // Show EXPLAIN output on queries
                'enabled' => false,
                'types' => ['SELECT'],     // ['SELECT', 'INSERT', 'UPDATE', 'DELETE']; for MySQL 5.6.3+
            ],
            'hints'             => false,    // Show hints for common mistakes
        ],
    ],

];
