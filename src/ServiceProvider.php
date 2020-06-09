<?php

namespace Butler\Service;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Symfony\Component\Finder\Finder;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->mergeApplicationConfig();

        $this->configureExtraConfig();

        $this->configureTimezone();

        $this->registerBaseProviders();

        $this->registerApplicationProviders();

        $this->registerExtraProviders();

        $this->registerExtraAliases();
    }

    public function boot()
    {
        $this->loadCommands();

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'service');

        $this->loadPublishing();
    }

    protected function mergeApplicationConfig()
    {
        if ($this->app->configurationIsCached()) {
            return;
        }

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

    protected function registerBaseProviders()
    {
        if (config('bugsnag.api_key', false)) {
            $this->app->register(\Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class);
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
}
