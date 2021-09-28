<?php

namespace Butler\Service\Tests;

use Butler\Audit\Facades\Auditor;
use Butler\Auth\AccessToken;
use Butler\Auth\ButlerAuth;
use Butler\Health\Checks as HealthChecks;
use Butler\Health\Repository as HealthRepository;
use Butler\Service\Models\Consumer;
use Butler\Service\Tests\TestCheck;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class ServiceProviderTest extends TestCase
{
    use ServiceProviderTrait;

    protected function setUp(): void
    {
        putenv('APP_RUNNING_IN_CONSOLE=true');

        parent::setUp();
    }

    public function test_routes_are_loaded()
    {
        $this->assertEquals('http://localhost', route('front'));
        $this->assertEquals('http://localhost/graphql', route('graphql'));
        $this->assertEquals('http://localhost/health', route('health'));
    }

    public function test_views_are_loaded()
    {
        $this->assertNotEmpty(view('service::front'));
    }

    public function test_mergeApplicationConfig_merges_source_and_application_config()
    {
        $this->assertEquals('log', config('butler.audit.driver'));
        $this->assertEquals('file', config('butler.guru.driver'));
        $this->assertFalse(config('butler.graphql.include_trace'));
        $this->assertEquals([], config('butler.guru.events'));
        $this->assertEquals('bar', config('butler.custom.foo'));
    }

    public function test_application_config_files_is_merged()
    {
        $this->assertEquals('foobar', config('session.table'));
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

    public function test_migration_paths_are_loaded()
    {
        $paths = app('migrator')->paths();

        $this->assertContains(realpath(__DIR__ . '/../database/migrations'), $paths);
        $this->assertContains(realpath(database_path('migrations/default')), $paths);
    }

    public function test_morph_map_is_registered()
    {
        $this->assertEquals([
            'consumer' => \Butler\Service\Models\Consumer::class,
        ], Relation::morphMap());
    }

    public function test_gate_abilities()
    {
        $this->assertTrue(Gate::has('graphql'));
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

        ButlerAuth::actingAs($this->makeConsumer(['name' => 'service1']));

        Auditor::fake();

        audit('foo', 123)->bar();

        Auditor::assertLogged('foo.bar', fn ($data)
            => $data->initiator === 'service1'
            && $data->hasInitiatorContext('ip', '127.0.0.1')
            && $data->hasInitiatorContext('userAgent', 'Symfony')
            && $data->hasInitiatorContext('tokenName', 'my token'));
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

    public function test_health_is_configured()
    {
        $this->assertFalse(config('butler.health.route'));

        $this->assertFalse(Route::has('butler-health'), 'butler-health route should not be registered');

        $this->assertNotEmpty((new HealthRepository())()['application']['butlerService']);

        $this->assertEquals(
            [
                HealthChecks\Database::class,
                HealthChecks\Redis::class,
                HealthChecks\FailedJobs::class,
                TestCheck::class,
            ],
            config('butler.health.checks'),
            '"Core" checks should be merged with "application" checks.'
        );
    }

    private function makeConsumer(array $attributes = []): Consumer
    {
        return new class ($attributes) extends Consumer {
            public function currentAccessToken(): AccessToken
            {
                return new AccessToken([
                    'token' => 'secret',
                    'abilities' => ['*'],
                    'name' => 'my token',
                ]);
            }
        };
    }
}
