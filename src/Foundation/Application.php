<?php

declare(strict_types=1);

namespace Butler\Service\Foundation;

use Butler\Service\Graphql\Exceptions\BackendValidation;
use Butler\Service\Http\Middleware\SetAcceptJson;
use Butler\Service\ServiceProvider;
use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Foundation\Configuration\ApplicationBuilder;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

class Application extends BaseApplication
{
    public static function configure(
        ?string $basePath = null,
        string $apiPrefix = 'api',
    ) {
        $basePath = match (true) {
            is_string($basePath) => $basePath,
            default => static::inferBasePath(),
        };

        $app = (new static($basePath))
            ->useConfigPath(realpath(__DIR__ . '/../config'))
            ->dontMergeFrameworkConfiguration();

        $routeFile = fn (string $path) => file_exists($path) ? $path : null;

        return (new ApplicationBuilder($app))
            ->withKernels()
            ->withEvents()
            ->withProviders()
            ->withCommands([
                \Butler\Service\Console\Commands\Assets::class,
                $app->path('Console/Commands'),
            ])
            ->withRouting(
                web: $routeFile($app->basePath('routes/web.php')),
                api: $routeFile($app->basePath('routes/api.php')),
                commands: $routeFile($app->basePath('routes/console.php')),
                apiPrefix: $apiPrefix,
                then: fn () => require __DIR__ . '/../routes.php',
            )
            ->withMiddleware(function (Middleware $middleware) {
                $middleware
                    ->redirectGuestsTo('/')
                    ->validateCsrfTokens(except: ['telescope/*'])
                    ->alias([
                        'set-accept-json' => SetAcceptJson::class,
                    ]);
            })
            ->withExceptions(function (Exceptions $exceptions) {
                $exceptions
                    ->dontReport(BackendValidation::class)
                    ->shouldRenderJsonWhen(fn (Request $request)
                        => $request->expectsJson() || $request->routeIs('graphql')
                    );
            });
    }

    public function registerConfiguredProviders()
    {
        $this->register($butlerService = new ServiceProvider($this));

        parent::registerConfiguredProviders();

        $butlerService->registerApplicationProviders();
    }
}
