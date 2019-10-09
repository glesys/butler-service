<?php

namespace Butler\Service\Testing\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;

trait MigratesDatabases
{
    public function migrateDatabase(string $database = 'default'): void
    {
        $this->artisan('migrate:fresh', [
            '--database' => $database,
            '--path' => "database/migrations/{$database}",
            '--seeder' => Str::studly("{$database}DatabaseSeeder"),
        ]);

        $this->app[Kernel::class]->setArtisan(null);
    }
}
