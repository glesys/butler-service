<?php

namespace Butler\Service\Foundation;

use Butler\Service\ServiceProvider;
use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{
    public function configPath($path = '')
    {
        return realpath(__DIR__ . '/../config');
    }

    public function registerConfiguredProviders()
    {
        $this->register($butlerService = new ServiceProvider($this));

        parent::registerConfiguredProviders();

        $butlerService->registerApplicationProviders();
        $butlerService->registerExtraProviders();
    }
}
