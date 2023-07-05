:construction: **Not ready for production.**

# Butler Service

A Laravel-based micro-framework for web services using GraphQL.

## Getting Started

*Requires a working Laravel app with a database connection.*

```shell
composer require glesys/butler-service
```

Replace `Illuminate\Foundation\Application` with `Butler\Service\Foundation\Application` in `bootstrap/app.php`.

```shell
php artisan vendor:publish --tag=butler-config --tag=butler-assets
php artisan migrate
```

It is optional (but recommended) to extend your `TestCase` (or whatever file that extends Laravels `TestCase`) with `Butler\Service\Testing\TestCase`.

```php
use Butler\Service\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
}
```

## Service Providers

All service providers in your `app/Providers` directory will be registered automatically.

## Config

You can use your configuration files as usual. See [src/config](src/config) for our defaults.

:information_source: Remember that your applications `config/butler.php` only merges the first level of the [default configuration](src/config/butler.php).

## Views

Views can be updated by publishing them:

```shell
php artisan vendor:publish --tag=butler-views
```

### Extra

If you dont want a `config/app.php` you can use `butler.service.extra` in `config/butler.php` to add "providers", "aliases" and "config". Note that "config" **will not** merge with existing config.

```php
    // example
    'providers' => [
        Foo\BarServiceProvider::class,
    ],
    'aliases' => [
        'Backend' => App\Facades\Backend::class,
    ],
    'config' => [
        'trustedproxy.proxies' => [
            '10.0.0.0/8',
        ],
    ],
```

## index.php

To keep your applications "index.php" up to date you can publish the one in butler-service.

:information_source: Maintenance mode is not supported.

```shell
php artisan vendor:publish --force --tag=butler-index
```

## Authentication with OAuth

Configure `butler.sso` in `config/butler.php`.

Set `butler.sso.fake` to `true` to fake to login process.

See [laravel/socialite](https://github.com/laravel/socialite) for more information.

## Authentication with butler-auth

```php
$consumer = \Butler\Service\Models\Consumer::create(['name' => 'Service A']);

$token = $consumer->createToken(abilities: ['*'], name: 'token-name')->plainTextToken;
```

See [butler-auth](https://github.com/glesys/butler-auth) for more information.

### Authorization

GraphQL operations are authorized by the "graphql" `Gate` ability defined in the [ServiceProvider](src/ServiceProvider.php).

```php
// allow "query" operations only
$consumer->createToken(['query'], 'my read-only token');

// allow "mutation" operations only
$consumer->createToken(['mutation'], 'my write-only token');

// allow any operations
$consumer->createToken(['*'], 'my full-access token');
$consumer->createToken(['query', 'mutation', 'subscription'], 'my graphql token');
```

## GraphQL with butler-graphql

See [butler-graphql](https://github.com/glesys/butler-graphql).

## Audit with butler-audit

See [butler-audit](https://github.com/glesys/butler-audit).

## Health checks with butler-health

See [butler-health](https://github.com/glesys/butler-health) for more information.

## Database host maintenance

For applications using multiple database hosts, you may add a `maintenance`
option on your database connection with a cron expression for when the host
with the same index as the expression should be in maintenance mode.

In the example below, "host1" will not be used by the application between 01:00 and 01:59.

```php
    // config/database.php
    'host' => ['host1', 'host2', 'host3'],
    'maintenance' => ['* 1 * * *', '* 2 * * *', '* 3 * * *'],
```

:information_source: When running i.e. laravel-octane you need to use the
[DisconnectFromDatabases](https://github.com/laravel/octane/blob/1.x/src/Listeners/DisconnectFromDatabases.php) listener.

## Testing

```shell
vendor/bin/phpunit
vendor/bin/pint --test
```

## How To Contribute

Development happens at GitHub; any typical workflow using Pull Requests are welcome. In the same spirit, we use the GitHub issue tracker for all reports (regardless of the nature of the report, feature request, bugs, etc.).

### Code standard

As the library is intended for use in Laravel applications we encourage code standard to follow [upstream Laravel practices](https://laravel.com/docs/master/contributions#coding-style) - in short that would mean [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) and [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md).
