<?php

namespace Butler\Service\Testing\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;

trait MigratesDatabases
{
    public function migrateDatabase(string $database = 'default'): void
    {
        if ($database === 'default' && ! is_dir(database_path('migrations/default'))) {
            $path = 'database/migrations';
        }

        $this->artisan('migrate:fresh', [
            '--database' => $database,
            '--path' => $path ?? "database/migrations/{$database}",
            '--seeder' => Str::studly("{$database}DatabaseSeeder"),
        ]);

        $this->app[Kernel::class]->setArtisan(null);
    }
}
