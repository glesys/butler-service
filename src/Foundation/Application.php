<?php

declare(strict_types=1);

namespace Butler\Service\Foundation;

use Butler\Service\Http\Middleware\Authenticate;
use Butler\Service\Http\Middleware\SetAcceptJson;
use Butler\Service\ServiceProvider;
use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Foundation\Configuration\ApplicationBuilder;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

class Application extends BaseApplication
{
    public static function configure(?string $basePath = null)
    {
        $basePath = match (true) {
            is_string($basePath) => $basePath,
            default => static::inferBasePath(),
        };

        $app = (new static($basePath))->useConfigPath(realpath(__DIR__ . '/../config'));

        return (new ApplicationBuilder($app))
            ->withKernels()
            ->withEvents()
            ->withProviders()
            ->withCommands([
                \Butler\Service\Console\Commands\Assets::class,
                $app->path('Console/Commands'),
            ])
            ->withRouting(
                web: $app->basePath('routes/web.php'),
                api: $app->basePath('routes/api.php'),
                commands: $app->basePath('routes/console.php'),
                then: fn () => require __DIR__ . '/../routes.php',
            )
            ->withMiddleware(function (Middleware $middleware) {
                $middleware
                    ->redirectGuestsTo('/')
                    ->validateCsrfTokens(except: ['telescope/*'])
                    ->alias([
                        'auth' => Authenticate::class,
                        'set-accept-json' => SetAcceptJson::class,
                    ]);
            })
            ->withExceptions(function (Exceptions $exceptions) {
                $exceptions->shouldRenderJsonWhen(fn (Request $request)
                    => $request->expectsJson() || $request->routeIs('graphql')
                );
            });
    }

    public function registerConfiguredProviders()
    {
        $this->register($butlerService = new ServiceProvider($this));

        parent::registerConfiguredProviders();

        $butlerService->registerApplicationProviders();
        $butlerService->registerExtraProviders();
        $butlerService->registerDatabaseManager();
        $butlerService->registerDatabaseConnectionFactory();
    }
}
