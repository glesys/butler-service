<?php

namespace Butler\Service;

use Butler\Audit\Facades\Auditor;
use Butler\Auth\Contracts\HasAccessTokens;
use Butler\Health\Checks as HealthChecks;
use Butler\Health\Repository as HealthRepository;
use Butler\Service\Listeners\FlushBugsnag;
use Composer\InstalledVersions;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Octane\Events\RequestTerminated;
use Symfony\Component\Finder\Finder;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->mergeApplicationConfig();

        $this->configureExtraConfig();

        $this->configureTimezone();

        $this->configureAudit();

        $this->configureHealth();

        $this->registerBaseProviders();

        $this->registerApplicationProviders();

        $this->registerExtraProviders();

        $this->registerExtraAliases();
    }

    public function boot()
    {
        $this->loadMigrations();

        $this->registerMorphMap();

        $this->loadCommands();

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'service');

        $this->loadPublishing();

        $this->defineGateAbilities();

        Blade::componentNamespace('Butler\\Service\\View\\Components', 'butler-service');
    }

    protected function mergeApplicationConfig()
    {
        if ($this->app->configurationIsCached()) {
            return;
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/butler.php', 'butler');

        $applicationConfigFiles = Finder::create()
            ->files()
            ->name('*.php')
            ->in(base_path('config'));

        foreach ($applicationConfigFiles as $file) {
            $key = basename($file->getRealPath(), '.php');

            config()->set($key, array_merge(
                config($key, []),
                require $file->getRealPath()
            ));
        }
    }

    protected function configureExtraConfig()
    {
        if ($this->app->configurationIsCached()) {
            return;
        }

        foreach (config('butler.service.extra.config', []) as $key => $value) {
            config()->set($key, $value);
        }
    }

    protected function configureTimezone()
    {
        // NOTE: To be able to override the timezone config we need to call
        // `date_default_timezone_set()`. Laravel already does this in the
        // LoadConfiguration bootstrapper but at that time we haven't yet merged
        // the config overrides.
        date_default_timezone_set(config('app.timezone'));
    }

    protected function configureAudit()
    {
        if (! $this->app->configurationIsCached()) {
            config(['butler.audit.default_initiator_resolver' => false]);
            config(['butler.audit.extend_bus_dispatcher' => true]);
        }

        $resolver = $this->app->runningInConsole()
            ? fn () => ['console', ['hostname' => gethostname()]]
            : function () {
                if (auth()->check()) {
                    $user = auth()->user();
                    return [
                        $user->name,
                        array_filter([
                            'ip' => request()->ip(),
                            'userAgent' => request()->userAgent(),
                            'tokenName' => optional($user->currentAccessToken())->name,
                        ])
                    ];
                }

                return [request()->ip(), ['userAgent' => request()->userAgent()]];
            };

        Auditor::initiatorResolver($resolver);
    }

    protected function configureHealth()
    {
        HealthRepository::customApplicationData(fn () => [
            'butlerService' => ltrim(InstalledVersions::getPrettyVersion('glesys/butler-service'), 'v'),
        ]);

        if ($this->app->configurationIsCached()) {
            return;
        }

        config(['butler.health.route' => false]);

        if (config('butler.health.core', true)) {
            config([
                'butler.health.checks' => array_merge([
                    HealthChecks\Database::class,
                    HealthChecks\Redis::class,
                    HealthChecks\FailedJobs::class,
                ], config('butler.health.checks', [])),
            ]);
        }
    }

    protected function registerBaseProviders()
    {
        if (config('bugsnag.api_key', false)) {
            $this->app->register(\Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class);

            // NOTE: Temporary fix until "bugsnag/bugsnag-laravel" supports octane.
            $this->app->singleton(FlushBugsnag::class);

            $this->app['config']->push(
                'octane.listeners.' . RequestTerminated::class,
                FlushBugsnag::class
            );
        }
    }

    protected function registerApplicationProviders()
    {
        if (! is_dir(app_path('Providers'))) {
            return;
        }

        $serviceProviderFiles = Finder::create()
            ->files()
            ->name('*Provider.php')
            ->in(app_path('Providers'));

        foreach ($serviceProviderFiles as $file) {
            $this->app->register("App\\Providers\\{$file->getBasename('.php')}");
        }
    }

    protected function registerExtraProviders()
    {
        foreach (config('butler.service.extra.providers', []) as $provider) {
            $this->app->register($provider);
        }
    }

    protected function registerExtraAliases()
    {
        $this->app->booting(function () {
            foreach (config('butler.service.extra.aliases', []) as $key => $alias) {
                \Illuminate\Foundation\AliasLoader::getInstance()->alias($key, $alias);
            }
        });
    }

    protected function loadMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(realpath(__DIR__ . '/../database/migrations'));

            if (is_dir($defaultPath = database_path('migrations/default'))) {
                $this->loadMigrationsFrom(realpath($defaultPath));
            }
        }
    }

    protected function registerMorphMap()
    {
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'consumer' => \Butler\Service\Models\Consumer::class,
        ]);
    }

    protected function loadCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Butler\Service\Console\Commands\GenerateUuid::class,
            ]);
        }
    }

    protected function loadPublishing()
    {
        if ($this->app->runningInConsole()) {
            $configSource = __DIR__ . '/../config/butler.php';
            $viewsSource = __DIR__ . '/../resources/views';

            $this->publishes([$configSource => base_path('config/butler.php')], 'config');
            $this->publishes([$viewsSource => resource_path('views/vendor/service')], 'views');
        }
    }

    public function defineGateAbilities()
    {
        Gate::define('graphql', function ($user, $operation) {
            if ($user instanceof HasAccessTokens) {
                return $user->tokenCan($operation);
            }

            return false;
        });
    }
}
