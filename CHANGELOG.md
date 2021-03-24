# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]


## [0.8.0] - 2021-03-24

### Changed
- Require Laravel 8.33.
- **BREAKING**: Replaced butler-auth with [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum).
  Existing JWT tokens will not work if not migrated or new tokens are created.
  The only way to authenticate now is with the `Authorization` header.
- **BREAKING**: Renamed test method `actingAsJwtUser` to `actingAsConsumer`.

## [0.7.0] - 2021-03-18

### Changed
- Use the correlation id from butler-audit when logging to graylog to enable tracing requests/executions into graylog.
- **BREAKING**: Require [butler-graphql](https://github.com/glesys/butler-graphql/blob/master/CHANGELOG.md) 5.0.

## [0.6.1] - 2021-02-18

### Added
- Abstract class `QueueableJob` that includes commonly used traits.

## [0.6.0] - 2021-02-09

### Added
- Custom `Bus\Dispatcher` to support the new job trait `WithCorrelationId`.

### Changed
- **BREAKING**: Require [butler-audit](https://github.com/glesys/butler-audit/blob/master/CHANGELOG.md#030---2021-02-04) 0.3.

## [0.5.0] - 2021-01-21

### Changed
- **BREAKING**: Require [butler-audit](https://github.com/glesys/butler-audit/blob/master/CHANGELOG.md#020---2021-01-20) 0.2.

## [0.4.1] - 2020-12-18

### Changed
- Require [butler-guru](https://github.com/glesys/butler-guru/blob/master/CHANGELOG.md) 0.7.

## [0.4.0] - 2020-12-14

### Added
- Butler Service version in "health" route.

### Changed
- Renamed "readme" route to "front" where health checks and graphql schema is shown.
- "health" route only returns JSON.

### Removed
- "health" view.
- "schema" view and route.

## [0.3.5] - 2020-12-10

## Added
- Support PHP 8

## [0.3.4] - 2020-11-20

### Changed
- Require [butler-audit](https://github.com/glesys/butler-audit/blob/master/CHANGELOG.md) 0.1.2.

## [0.3.3] - 2020-11-13

### Changed
- Audit initiator resolver now supports anonymous users and when running in console.

## [0.3.2] - 2020-11-12

### Changed
- Add correlation id for all requests sent with the graphql client.

## [0.3.1] - 2020-11-09

### Added
- Include [butler-audit](https://github.com/glesys/butler-audit)

## [0.3.0] - 2020-10-08

### Changed
- Require Laravel 8.

### Added
- AMQP config file

## [0.2.13] - 2020-08-03

### Added
- `actingAsJwtUser` helper for tests.

## [0.2.12] - 2020-06-09

### Added
- Service providers in `app\Providers` will be registered automatically.

## [0.2.11] - 2020-06-03

### Changed
- Return unknown state for Redis health check when redis host is undefined.

## [0.2.9] - 2020-05-13

### Changed
- Renamed health check "status" to "state".

## [0.2.8] - 2020-05-07

### Changed
- `MigratesDatabases` trait now supports default database migrations directly in `database/migrations`.
- `MigratesDatabases` trait now supports default database seeder (`DatabaseSeeder.php`) for the default database connection.

## [0.2.7] - 2020-05-05

### Fixed
- Use correct namespace for GraylogLoggerFactory

## [0.2.6] - 2020-04-30

### Fixed
- Support overriding timezone.

## [0.2.5] - 2020-04-29

### Added
- Health checks.

### Changed
- `routes/web.php` and `routes/api.php` is no longer required.

## [0.2.3] - 2020-04-20

### Changed
- `routes/console.php` is no longer required.
- Use `BUGSNAG_API_KEY` instead of `BUTLER_SERVICE_BUGSNAG` when determining to register bugsnag or not.
- Use `GRAYLOG_HOST` instead of `BUTLER_SERVICE_GRAYLOG`.

## [0.2.2] - 2020-04-17

### Added
- Bugsnag default config

## [0.2.1] - 2020-04-16

### Added
- Github action

### Fixed
- Use correct config path in routes.php

## [0.2.0] - 2020-04-15

### Changed
- `Butler\Service\ServiceProvider` is now loaded before any other configured providers.

## [0.1.0] - 2020-04-15

### Added
- Initial release
