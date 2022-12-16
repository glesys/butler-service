<?php

namespace Butler\Service\Tests;

use Butler\Audit\ServiceProvider as AuditServiceProvider;
use Butler\Auth\ServiceProvider as AuthServiceProvider;
use Butler\Graphql\ServiceProvider as GraphqlServiceProvider;
use Butler\Health\ServiceProvider as HealthServiceProvider;
use Butler\Service\ServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\SocialiteServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends AbstractPackageTestCase
{
    public static function setUpBeforeClass(): void
    {
        $reflection = new \ReflectionClass(OrchestraTestCase::class);
        $appPath = dirname($reflection->getFileName(), 2) . '/laravel/';

        static::createRequiredTestDirectories($appPath);
        static::createRequiredTestFiles($appPath);
    }

    protected function getServiceProviderClass()
    {
        return ServiceProvider::class;
    }

    protected function getRequiredServiceProviders($app)
    {
        return [
            AuditServiceProvider::class,
            AuthServiceProvider::class,
            GraphqlServiceProvider::class,
            HealthServiceProvider::class,
            SocialiteServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->addLoginRouteUsedByOrchestra();

        $app->config->set('database.default', 'default');
        $app->config->set('database.connections.default', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function actingAsUser(array $data = [])
    {
        $user = new GenericUser(array_merge([
            'id' => rand(0, 999),
            'username' => 'username',
            'name' => 'name',
            'email' => 'user@example.com',
            'oauth_token' => 'token',
            'oauth_refresh_token' => 'refresh-token',
            'remember_token' => null,
        ], $data));

        return $this->actingAs($user, 'web');
    }

    private static function createRequiredTestDirectories(string $appPath): void
    {
        $directories = [
            'app/Http/Graphql/Mutations',
            'app/Http/Graphql/Queries',
            'app/Providers',
            'database/migrations/default',
        ];

        foreach ($directories as $directory) {
            if (! is_dir($appPath . $directory)) {
                mkdir($appPath . $directory, 0777, true);
            }
        }
    }

    private static function createRequiredTestFiles(string $appPath): void
    {
        $files = [
            'config/auth.php' => 'config/auth.php',
            'config/butler.php' => 'config/butler.php',
            'config/session.php' => 'config/session.php',
            'schema.graphql' => 'app/Http/Graphql/schema.graphql',
            'StartMutation.txt' => 'app/Http/Graphql/Mutations/Start.php',
            'PingQuery.txt' => 'app/Http/Graphql/Queries/Ping.php',
            'AppServiceProvider.txt' => 'app/Providers/AppServiceProvider.php',
        ];

        foreach ($files as $name => $path) {
            copy(__DIR__ . '/' . $name, $appPath . $path);
        }
    }

    private function addLoginRouteUsedByOrchestra(): void
    {
        Route::middleware('web')
            ->get('orchestra-login', fn () => [])
            ->name('login');
    }
}
