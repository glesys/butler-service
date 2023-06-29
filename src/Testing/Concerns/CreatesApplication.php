<?php

declare(strict_types=1);

namespace Butler\Service\Testing\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;

trait CreatesApplication
{
    /**
     * This method must return the absolute path to your applications bootstrap file.
     */
    protected function bootstrapFilePath(): string
    {
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);

        return dirname($reflection->getFileName(), 3) . '/bootstrap/app.php';
    }

    public function createApplication(): Application
    {
        $app = require $this->bootstrapFilePath();

        $app->make(Kernel::class)->bootstrap();

        Hash::driver('bcrypt')->setRounds(4);

        return $app;
    }
}
