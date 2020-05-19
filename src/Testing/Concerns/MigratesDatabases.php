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
            '--path' => $this->migrationsPath($database),
            '--seeder' => $this->seederName($database),
        ]);

        $this->app[Kernel::class]->setArtisan(null);
    }

    private function migrationsPath(string $database): string
    {
        if ($database === 'default' && ! is_dir(database_path('migrations/default'))) {
            return 'database/migrations';
        }

        return "database/migrations/{$database}";
    }

    private function seederName(string $database): string
    {
        if ($database === 'default' && ! is_file(database_path('seeds/DefaultDatabaseSeeder.php'))) {
            return 'DatabaseSeeder';
        }

        return Str::studly("{$database}DatabaseSeeder");
    }
}
