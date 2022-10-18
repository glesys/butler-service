<?php

declare(strict_types=1);

namespace Butler\Service\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            if (is_file($apiRoutes = base_path('routes/api.php'))) {
                Route::middleware('api')->group($apiRoutes);
            }

            if (is_file($webRoutes = base_path('routes/web.php'))) {
                Route::middleware('web')->group($webRoutes);
            }
        });
    }
}
