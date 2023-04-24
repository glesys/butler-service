<?php

declare(strict_types=1);

namespace App;

use Butler\Service\Foundation\Application as ButlerApplication;
use Illuminate\Foundation\PackageManifest;

class Application extends ButlerApplication
{
    protected function registerBaseBindings()
    {
        parent::registerBaseBindings();

        $this->app->extend(PackageManifest::class, function (PackageManifest $service) {
            $service->vendorPath = realpath(__DIR__ . '/../../../vendor');

            return $service;
        });
    }
}
