<?php

namespace Butler\Service\Tests;

use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Illuminate\Support\Facades\Cache;

class ServiceProviderTest extends TestCase
{
    use ServiceProviderTrait;

    public function test_routes_are_loaded()
    {
        $this->assertEquals('http://localhost', route('readme'));
        $this->assertEquals('http://localhost/schema', route('schema'));
        $this->assertEquals('http://localhost/graphql', route('graphql'));
    }

    public function test_views_are_loaded()
    {
        $this->assertNotEmpty(view('service::readme'));
        $this->assertNotEmpty(view('service::schema'));
    }

    public function test_extra_config_is_configured()
    {
        $this->assertEquals('bar', config('foo'));
    }

    public function test_extra_providers_are_registered()
    {
        $this->assertInstanceOf(
            FoobarServiceProvider::class,
            app()->getProvider(FoobarServiceProvider::class)
        );
    }

    public function test_extra_aliases_are_registered()
    {
        $this->assertInstanceOf(Cache::class, app('Foobar'));
    }

    public function test_application_config_merges_butler_service_config()
    {
        $this->assertEquals('foobar', config('session.path'));
    }

    /**
     * @dataProvider butlerServiceConfigProvider
     */
    public function test_butler_service_config($configKey, $expectedValue)
    {
        $this->assertEquals($expectedValue, config($configKey));
    }

    public function butlerServiceConfigProvider()
    {
        return [
            ['butler.service.graylog', false],
            ['butler.service.horizon', false],
            ['butler.service.routes.readme', '/'],
            ['butler.service.routes.schema', '/schema'],
            ['butler.service.routes.graphql', '/graphql'],
            ['butler.service.extra.config', ['foo' => 'bar']],
            ['butler.service.extra.aliases', ['Foobar' => Cache::class]],
            ['butler.service.extra.providers', [FoobarServiceProvider::class]],
        ];
    }
}
