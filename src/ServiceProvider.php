<?php

namespace Butler\Service;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerBaseProviders();

        $this->configureExtraConfig();

        $this->registerExtraAliases();

        $this->registerExtraProviders();
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

        $applicationConfigFiles = \Symfony\Component\Finder\Finder::create()
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

    protected function registerBaseProviders()
    {
        if (config('bugsnag.api_key', false)) {
            $this->app->register(\Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class);
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

    protected function registerExtraAliases()
    {
        $this->app->booting(function () {
            foreach (config('butler.service.extra.aliases', []) as $key => $alias) {
                \Illuminate\Foundation\AliasLoader::getInstance()->alias($key, $alias);
            }
        });
    }

    protected function registerExtraProviders()
    {
        foreach (config('butler.service.extra.providers', []) as $provider) {
            $this->app->register($provider);
        }
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
