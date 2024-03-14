<?php

declare(strict_types=1);

namespace Butler\Service;

use Bugsnag\BugsnagLaravel\BugsnagServiceProvider;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Butler\Audit\Facades\Auditor;
use Butler\Auth\Contracts\HasAccessTokens;
use Butler\Health\Checks as HealthChecks;
use Butler\Health\Repository as HealthRepository;
use Butler\Service\Auth\SessionUser;
use Butler\Service\Auth\SessionUserProvider;
use Butler\Service\Listeners\FlushBugsnag;
use Butler\Service\Models\Consumer;
use Butler\Service\Socialite\FakeProvider;
use Butler\Service\Socialite\PassportProvider;
use Composer\InstalledVersions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
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
    }

    public function boot()
    {
        $this->loadMigrations();

        $this->registerMorphMap();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'butler');

        $this->loadPublishing();

        $this->defineGateAbilities();

        $this->registerBugsnagCallback();

        if (config('butler.sso.enabled')) {
            $this->registerSocialiteDriver();
        }

        $this->registerSessionUserProvider();

        $this->addHealthInformation();

        Model::shouldBeStrict(! $this->app->isProduction());
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
                    /** @var SessionUser|Consumer */
                    $user = auth()->user();

                    return [
                        $user instanceof SessionUser ? $user->email : $user->name,
                        array_filter([
                            'ip' => request()->ip(),
                            'userAgent' => request()->userAgent(),
                            'tokenName' => $user instanceof HasAccessTokens
                                ? $user->currentAccessToken()?->name
                                : null,
                        ]),
                    ];
                }

                return [request()->ip(), ['userAgent' => request()->userAgent()]];
            };

        Auditor::initiatorResolver($resolver);
    }

    protected function configureHealth()
    {
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
            $this->app->register(BugsnagServiceProvider::class);

            // NOTE: Temporary fix until "bugsnag/bugsnag-laravel" supports octane.
            $this->app->singleton(FlushBugsnag::class);

            $this->app['config']->push(
                'octane.listeners.' . RequestTerminated::class,
                FlushBugsnag::class
            );
        }
    }

    public function registerApplicationProviders()
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

    protected function loadPublishing()
    {
        if ($this->app->runningInConsole()) {
            $configSource = __DIR__ . '/../config/butler.php';
            $indexSource = __DIR__ . '/../public/index.php';
            $assetsSource = __DIR__ . '/../public/vendor/butler';
            $viewsSource = __DIR__ . '/../resources/views';

            $this->publishes([$configSource => base_path('config/butler.php')], 'butler-config');
            $this->publishes([$indexSource => public_path('index.php')], 'butler-index');
            $this->publishes([$assetsSource => public_path('vendor/butler')], 'butler-assets');
            $this->publishes([$viewsSource => resource_path('views/vendor/butler')], 'butler-views');
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

    public function registerBugsnagCallback()
    {
        if (! $this->app->providerIsLoaded(BugsnagServiceProvider::class)) {
            return;
        }

        if (config('butler.service.ignore_bugsnag_for_email_consumer', true)) {
            Bugsnag::registerCallback(new \Butler\Service\Bugsnag\Middlewares\IgnoreEmailConsumer());
        }
    }

    public function registerSocialiteDriver()
    {
        if (! $this->app->has(SocialiteFactory::class)) {
            return;
        }

        $socialite = $this->app->make(SocialiteFactory::class);
        $config = config('butler.sso');

        $provider = $this->app->isLocal() && ($config['fake'] ?? false)
            ? FakeProvider::class
            : PassportProvider::class;

        $socialite->extend(
            'passport',
            fn () => $socialite
                ->buildProvider($provider, $config)
                ->setHost($config['url'])
        );
    }

    public function registerSessionUserProvider()
    {
        Auth::provider('session', fn () => new SessionUserProvider());
    }

    public function addHealthInformation(): void
    {
        HealthRepository::add('butlerService', [
            'version' => ltrim(InstalledVersions::getPrettyVersion('glesys/butler-service'), 'v'),
        ]);

        HealthRepository::add('laravelOctane', [
            'version' => ltrim(InstalledVersions::getPrettyVersion('laravel/octane'), 'v'),
            'running' => (int) getenv('LARAVEL_OCTANE') === 1,
        ]);

        if (function_exists('swoole_version')) {
            HealthRepository::add('laravelOctane', ['swoole' => swoole_version()]);
        }
    }
}
