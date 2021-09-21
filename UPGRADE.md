## Upgrade from v0.11 to v0.12

### BREAKING: Require glesys/butler-audit [v0.4](https://github.com/glesys/butler-audit/blob/master/CHANGELOG.md#040---2021-09-23)

1. Use `Butler\Audit\Bus\WithCorrelationId` instead of `Butler\Service\Bus\WithCorrelationId`.

### BREAKING: Use [glesys/butler-health](https://github.com/glesys/butler-health)

1. Move your health checks (if any) from `butler.service.health.checks` to `butler.health.checks`.
1. Remove `butler.service.health` from your configuration.
1. Use `Butler\Health\Repository` instead of `Butler\Service\Repositories\HealthRepository`.

## Upgrade from v0.10 to v0.11

### BREAKING: Replace laravel/sanctum with [glesys/butler-auth](https://github.com/glesys/butler-audit/blob/master/CHANGELOG.md)

1. Rename table `personal_access_tokens` to `access_tokens`.
1. Use `Butler\Auth\ButlerAuth` instead of `Laravel\Sanctum\Sanctum`.
1. If you have custom `auth` configuration, make sure to change the driver of your guard from `sanctum` to `butler`.
