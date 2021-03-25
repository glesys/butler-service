:construction: **Not ready for production.**

# Butler Service

A Laravel-based micro-framework for web services using GraphQL and RabbitMQ.

## Getting Started

*Requires a working Laravel app with a database connection.*

```shell
composer require glesys/butler-service
php artisan vendor:publish --provider="Butler\Service\ServiceProvider" --tag=config
php artisan migrate
```

Replace `Illuminate\Foundation\Application` with `Butler\Service\Foundation\Application` in `bootstrap/app.php`.

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

You can use your configuration files as usual. See `./src/config/` for our defaults.

### Default routes

There are 3 default routes; "front", "graphql" and "health".
They can be updated at `butler.service.routes` in `config/butler.php`.

For example you might want to listen for graphql on `/api` instead of `/graphql`.

Their views can be updated by publishing them:

```shell
php artisan vendor:publish --provider="Butler\Service\ServiceProvider" --tag=views
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

### Health Checks

Butler Service comes with some [default health checks](src/Health/Checks) enabled.
Check results is listed on the "front" route but can also be accessed via the "health" route as JSON.

Add your own health checks by extending `Butler\Service\Health\Check` and add it to
the `butler.service.health.checks` configuration.

```php
    'health' => [
        'checks' => [
            App\Health\MyCheck::class,
        ],
    ],
```

## Authentication with Laravel Sanctum

```php
$user = \Butler\Service\Models\Consumer::create(['name' => 'Service A']);

$token = $user->createToken('token-name', ['*'])->plainTextToken;
```

See [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum) for more information.

### Authorization

GraphQL operations are authorized by the "graphql" `Gate` ability defined in the [ServiceProvider](src/ServiceProvider.php).

```php
// allow "query" operations only
$consumer->createToken('read-only', ['query']);

// allow "mutation" operations only
$consumer->createToken('write-only', ['mutation']);

// allow any operations
$consumer->createToken('full-access', ['*']);
$consumer->createToken('full-graphql', ['query', 'mutation', 'subscription']);
```

## GraphQL with butler-graphql

See [butler-graphql](https://github.com/glesys/butler-graphql).

## RabbitMQ with butler-guru

See [butler-guru](https://github.com/glesys/butler-guru).

## Audit with butler-audit

See [butler-audit](https://github.com/glesys/butler-audit).

### Queued jobs and correlation ID

The trait `WithCorrelationId` can be used on queable jobs that needs the same correlation id as the request.

## Testing

```shell
vendor/bin/phpunit
vendor/bin/phpcs
```

## How To Contribute

Development happens at GitHub; any typical workflow using Pull Requests are welcome. In the same spirit, we use the GitHub issue tracker for all reports (regardless of the nature of the report, feature request, bugs, etc.).

### Code standard

As the library is intended for use in Laravel applications we encourage code standard to follow [upstream Laravel practices](https://laravel.com/docs/master/contributions#coding-style) - in short that would mean [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) and [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md).
