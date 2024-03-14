## Upgrade from v0.26 to v0.27

### Update `bootstrap/app.php`

Replace everything with:

```php
return Butler\Service\Foundation\Application::configure()->create();
```

### Copy files

1. [public/index.php](https://github.com/laravel/laravel/blob/11.x/public/index.php)
1. [artisan](https://github.com/laravel/laravel/blob/11.x/artisan)

### Remove files

*Use `bootstrap/app.php` instead*

* `app/Http/Kernel.php`
* `app/Console/Kernel.php`
* `app/Exceptions/Handler.php`

### Config changes

* Use `CACHE_STORE` instead of `CACHE_DRIVER`
* `MAIL_MAILER` now defaults to "log"
* Removed `butler.service.extra.aliases` and `butler.service.extra.providers`

### Additional breaking changes

* [butler-health](https://github.com/glesys/butler-health/blob/main/CHANGELOG.md#060---2024-05-28)
* [laravel](https://laravel.com/docs/11.x/upgrade)

## Upgrade from v0.12 to v0.13

### BREAKING: Require glesys/butler-audit [v0.4](https://github.com/glesys/butler-audit/blob/master/CHANGELOG.md#040---2021-09-23)

1. Use `Butler\Audit\Bus\WithCorrelationId` instead of `Butler\Service\Bus\WithCorrelationId`.

## Upgrade from v0.11 to v0.12

### BREAKING: Use [glesys/butler-health](https://github.com/glesys/butler-health)

1. Move your health checks (if any) from `butler.service.health.checks` to `butler.health.checks`.
1. Remove `butler.service.health` from your configuration.
1. Use `Butler\Health\Repository` instead of `Butler\Service\Repositories\HealthRepository`.

## Upgrade from v0.10 to v0.11

### BREAKING: Replace laravel/sanctum with [glesys/butler-auth](https://github.com/glesys/butler-auth/blob/master/CHANGELOG.md)

1. Rename table `personal_access_tokens` to `access_tokens`.
1. Use `Butler\Auth\ButlerAuth` instead of `Laravel\Sanctum\Sanctum`.
1. If you have custom `auth` configuration, make sure to change the driver of your guard from `sanctum` to `butler`.
