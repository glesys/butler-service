<?php

declare(strict_types=1);

namespace Butler\Service\Tests;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Butler\Audit\Facades\Auditor;
use Butler\Auth\AccessToken;
use Butler\Auth\ButlerAuth;
use Butler\Health\Checks as HealthChecks;
use Butler\Service\Models\Consumer;
use Butler\Service\ServiceProvider as ButlerServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Mockery;

class ServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('APP_RUNNING_IN_CONSOLE=true');

        parent::setUp();
    }

    public function test_routes_are_loaded()
    {
        $this->assertEquals('http://localhost', route('home'));
        $this->assertEquals('http://localhost/graphql', route('graphql'));
        $this->assertEquals('http://localhost/health', route('health'));
    }

    public function test_views_are_loaded()
    {
        $this->assertNotEmpty(view('butler::home'));
    }

    public function test_mergeApplicationConfig_merges_source_and_application_config()
    {
        $this->assertEquals('log', config('butler.audit.driver'));
        $this->assertFalse(config('butler.graphql.include_trace'));
        $this->assertEquals('bar', config('butler.custom.foo'));
    }

    public function test_application_config_files_is_merged()
    {
        $this->assertEquals('sessions', config('session.table'));
        $this->assertEquals('baz', config('session.foobar'));
    }

    public function test_extra_config_is_configured()
    {
        $this->assertEquals('bar', config('foo'));
    }

    public function test_application_providers_are_registered()
    {
        $this->assertInstanceOf(
            \App\Providers\FoobarServiceProvider::class,
            app()->getProvider(\App\Providers\FoobarServiceProvider::class)
        );
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

    public function test_configureAudit_configures_audit()
    {
        $this->assertFalse(config('butler.audit.default_initiator_resolver'));
        $this->assertTrue(config('butler.audit.extend_bus_dispatcher'));
    }

    public function test_configureAudit_sets_initiator_resolver_for_console()
    {
        Auditor::fake();

        audit('foo', 123)->bar();

        Auditor::assertLogged('foo.bar', fn ($data)
            => $data->initiator === 'console'
            && $data->hasInitiatorContext('hostname', gethostname()));
    }

    public function test_configureAudit_sets_initiator_resolver_for_authenticated_user()
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

    public function test_configureAudit_sets_initiator_resolver_for_unauthenticated_user()
    {
        putenv('APP_RUNNING_IN_CONSOLE=false');

        $this->refreshApplication();

        Auditor::fake();

        audit('foo', 123)->bar();

        Auditor::assertLogged('foo.bar', fn ($data)
            => $data->initiator === '127.0.0.1'
            && $data->hasInitiatorContext('userAgent', 'Symfony'));
    }

    public function test_configureHealth_configures_health()
    {
        $this->assertFalse(config('butler.health.route'));

        $this->assertFalse(Route::has('butler-health'), 'butler-health route should not be registered');

        $this->assertEquals(
            [
                HealthChecks\Database::class,
                HealthChecks\Redis::class,
                HealthChecks\FailedJobs::class,
                \App\TestCheck::class,
            ],
            config('butler.health.checks'),
            '"Core" checks should be merged with "application" checks.'
        );
    }

    public function test_bugsnag_callbacks_is_registered_if_bugsnag_is_loaded()
    {
        $this->app->register(\Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class);

        Bugsnag::shouldReceive('registerCallback')
            ->once()
            ->with(Mockery::type(\Butler\Service\Bugsnag\Middlewares\IgnoreEmailConsumer::class));

        app()->getProvider(ButlerServiceProvider::class)->registerBugsnagCallback();
    }

    public function test_bugsnag_callbacks_is_not_registered_if_bugsnag_is_not_loaded()
    {
        Bugsnag::shouldReceive('registerCallback')->never();

        app()->getProvider(ButlerServiceProvider::class)->registerBugsnagCallback();
    }

    public function test_bugsnag_callbacks_is_not_registered_if_configured_not_to()
    {
        config(['butler.service.ignore_bugsnag_for_email_consumer' => false]);

        $this->app->register(\Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class);

        Bugsnag::shouldReceive('registerCallback')->never();

        app()->getProvider(ButlerServiceProvider::class)->registerBugsnagCallback();
    }

    public function test_eloquent_strictness()
    {
        $this->assertTrue(Model::preventsLazyLoading());
        $this->assertTrue(Model::preventsSilentlyDiscardingAttributes());
        $this->assertTrue(Model::preventsAccessingMissingAttributes());
    }

    private function makeConsumer(array $attributes = []): Consumer
    {
        return new class($attributes) extends Consumer
        {
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
