<?php

namespace Butler\Service\Tests;

use Butler\Graphql\ServiceProvider as GraphqlServiceProvider;
use Butler\Service\ServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

abstract class TestCase extends AbstractPackageTestCase
{
    protected function setUp(): void
    {
        $this->setUpButlerService();

        parent::setUp();
    }

    protected function getServiceProviderClass($app)
    {
        return ServiceProvider::class;
    }

    protected function getRequiredServiceProviders($app)
    {
        return [
            GraphqlServiceProvider::class,
        ];
    }

    private function setUpButlerService()
    {
        $reflection = new \ReflectionClass(\Orchestra\Testbench\TestCase::class);

        $orchestraPath = dirname($reflection->getFileName(), 2) . '/laravel';

        if (! is_dir($orchestraPath . '/app/Http/Graphql')) {
            mkdir($orchestraPath . '/app/Http/Graphql', 0777, true);
        }

        copy(__DIR__ . '/schema.graphql', $orchestraPath . '/app/Http/Graphql/schema.graphql');
        copy(__DIR__ . '/config/butler.php', $orchestraPath . '/config/butler.php');
        copy(__DIR__ . '/config/session.php', $orchestraPath . '/config/session.php');
    }
}
