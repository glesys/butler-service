# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
