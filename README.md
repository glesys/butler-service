:construction: **Not ready for production.**

# Butler Service

A Laravel-based micro-framework for web services using JWT, GraphQL and RabbitMQ.

## Getting Started

*Requires a working Laravel app.*

```shell
composer require glesys/butler-service
php artisan vendor:publish --provider="Butler\Service\ServiceProvider" --tag=config
php artisan butler-auth:generate-secret-key
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

## Config

You can use your configuration files as usual. See `./src/config/` for our defaults.

### Default routes

There are 4 default routes; "readme", "schema", "graphql" and "health".
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
        App\Providers\TelescopeServiceProvider::class,
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
Check results can be accessed via the "/health" endpoint and can return both HTML and JSON.

Add your own health checks by extending `Butler\Service\Health\Check` and add it to
the `butler.service.health.checks` configuration.

```php
    'health_checks' => [
        App\Health\MyCheck::class,
    ],
```

## JWT authentication with butler-auth

Generate tokens with `php artisan butler-auth:generate-token`.

See [butler-auth](https://github.com/glesys/butler-auth).

## GraphQL with butler-graphql

See [butler-graphql](https://github.com/glesys/butler-graphql).

## RabbitMQ with butler-guru

See [butler-guru](https://github.com/glesys/butler-guru).

## Testing

```shell
vendor/bin/phpunit
vendor/bin/phpcs
```

## How To Contribute

Development happens at GitHub; any typical workflow using Pull Requests are welcome. In the same spirit, we use the GitHub issue tracker for all reports (regardless of the nature of the report, feature request, bugs, etc.).

### Code standard

As the library is intended for use in Laravel applications we encourage code standard to follow [upstream Laravel practices](https://laravel.com/docs/master/contributions#coding-style) - in short that would mean [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) and [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md).
