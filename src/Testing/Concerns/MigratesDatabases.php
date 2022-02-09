<?php

namespace Butler\Service\Testing\Concerns;

use Illuminate\Contracts\Console\Kernel;

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
        return $database === 'default' ? '' : "database/migrations/{$database}";
    }

    private function seederName(string $database): string
    {
        if ($database === 'default' && ! is_file(database_path('seeders/DefaultDatabaseSeeder.php'))) {
            return 'DatabaseSeeder';
        }

        return str("{$database}DatabaseSeeder")->studly();
    }
}
