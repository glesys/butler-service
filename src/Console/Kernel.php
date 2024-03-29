<?php

declare(strict_types=1);

namespace Butler\Service\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

abstract class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $childKernelName = (new \ReflectionClass(get_called_class()))->getFileName();

        $this->load(dirname($childKernelName) . '/Commands');

        if (is_file($routes = base_path('routes/console.php'))) {
            require $routes;
        }
    }
}
