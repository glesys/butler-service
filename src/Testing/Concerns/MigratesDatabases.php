<?php

declare(strict_types=1);

namespace Butler\Service\Testing\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\File;

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

    protected function migrationsPath(string $database): string
    {
        return $database === 'default' ? '' : "database/migrations/{$database}";
    }

    protected function seederName(string $database): string
    {
        if ($database === 'default' && File::missing(database_path('seeders/DefaultDatabaseSeeder.php'))) {
            return 'DatabaseSeeder';
        }

        return str("{$database}DatabaseSeeder")->studly()->toString();
    }
}
