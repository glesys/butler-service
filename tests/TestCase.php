<?php

namespace Butler\Service\Tests;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Butler\Service\ServiceProvider;

abstract class TestCase extends AbstractPackageTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        copy(__DIR__ . '/config/butler.php', config_path('butler.php'));
        copy(__DIR__ . '/config/session.php', config_path('session.php'));

        if (! file_exists(app_path('Http/Graphql'))) {
            mkdir(app_path('Http/Graphql'), 0777, true);
            touch(app_path('Http/Graphql/schema.graphql'));
        }
    }

    protected function getServiceProviderClass($app)
    {
        return ServiceProvider::class;
    }
}
