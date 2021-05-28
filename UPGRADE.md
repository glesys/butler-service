## Upgrade from v0.10 to v0.11

### BREAKING: Replace laravel/sanctum with [glesys/butler-auth](https://github.com/glesys/butler-audit/blob/master/CHANGELOG.md)

1. Rename table `personal_access_tokens` to `access_tokens`.
1. Use `Butler\Auth\ButlerAuth` instead of `Laravel\Sanctum\Sanctum`.
1. If you got custom `auth` config, make sure you have `auth.guards.butler` set.
1. Remove traces of laravel/sanctum.
