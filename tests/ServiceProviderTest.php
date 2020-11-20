<?php

namespace Butler\Service\Tests;

use Butler\Audit\Facades\Auditor;
use Butler\Auth\JwtUser;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Illuminate\Support\Carbon;
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

    public function test_application_providers_are_registered()
    {
        $this->assertInstanceOf(
            \App\Providers\AppServiceProvider::class,
            app()->getProvider(\App\Providers\AppServiceProvider::class)
        );
    }

    public function test_extra_providers_are_registered()
    {
        $this->assertInstanceOf(
            ExtraServiceProvider::class,
            app()->getProvider(ExtraServiceProvider::class)
        );
    }

    public function test_extra_aliases_are_registered()
    {
        $this->assertInstanceOf(Cache::class, app('Foobar'));
    }

    public function test_application_config_merges_butler_service_config()
    {
        $this->assertEquals('foobar', config('session.table'));
    }

    public function test_can_override_timezone()
    {
        $this->assertEquals('Europe/Stockholm', config('app.timezone'));
        $this->assertEquals('Europe/Stockholm', Carbon::now()->getTimezone());
    }

    public function test_audit_initiator_resolver_resolves_console()
    {
        Auditor::fake();

        audit('foo', 123)->bar();

        Auditor::assertLogged('foo.bar', fn ($data)
            => $data->initiator === 'console'
            && $data->hasInitiatorContext('hostname', gethostname()));
    }

    public function test_audit_initiator_resolver_resolves_authenticated_user()
    {
        putenv('APP_RUNNING_IN_CONSOLE=false');

        $this->refreshApplication();
        $this->actingAs(new JwtUser(['sub' => 'service1']));

        Auditor::fake();

        audit('foo', 123)->bar();

        Auditor::assertLogged('foo.bar', fn ($data)
            => $data->initiator === 'service1'
            && $data->hasInitiatorContext('ip', '127.0.0.1')
            && $data->hasInitiatorContext('userAgent', 'Symfony'));
    }

    public function test_audit_initiator_resolver_resolves_unauthenticated_user()
    {
        putenv('APP_RUNNING_IN_CONSOLE=false');

        $this->refreshApplication();

        Auditor::fake();

        audit('foo', 123)->bar();

        Auditor::assertLogged('foo.bar', fn ($data)
            => $data->initiator === '127.0.0.1'
            && $data->hasInitiatorContext('userAgent', 'Symfony'));
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
            ['butler.service.routes.readme', '/'],
            ['butler.service.routes.schema', '/schema'],
            ['butler.service.routes.graphql', '/graphql'],
            ['butler.service.routes.health', '/health'],
            ['butler.service.health.checks', [TestCheck::class]],
            ['butler.service.extra.config', [
                'app.timezone' => 'Europe/Stockholm',
                'foo' => 'bar'
            ]],
            ['butler.service.extra.aliases', ['Foobar' => Cache::class]],
            ['butler.service.extra.providers', [ExtraServiceProvider::class]],
        ];
    }
}
