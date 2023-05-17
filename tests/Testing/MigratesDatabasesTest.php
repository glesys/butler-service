<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Testing;

use Butler\Service\Testing\Concerns\MigratesDatabases;
use Butler\Service\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Fluent;

class MigratesDatabasesTest extends TestCase
{
    public function test_migrateDatabase_with_default_argument()
    {
        $class = tap(new DummyTestCase())->migrateDatabase();

        $this->assertCount(1, $class->called);
        $this->assertEquals([
            'migrate:fresh',
            [
                '--database' => 'default',
                '--path' => '',
                '--seeder' => 'DatabaseSeeder',
            ],
        ], $class->called[0]);
    }

    public function test_migrateDatabase_with_argument()
    {
        $class = tap(new DummyTestCase())->migrateDatabase('legacy');

        $this->assertCount(1, $class->called);
        $this->assertEquals([
            'migrate:fresh',
            [
                '--database' => 'legacy',
                '--path' => 'database/migrations/legacy',
                '--seeder' => 'LegacyDatabaseSeeder',
            ],
        ], $class->called[0]);
    }

    public function test_migrateDatabase_with_named_default_seeder()
    {
        File::expects('missing')->andReturnFalse();

        $class = tap(new DummyTestCase())->migrateDatabase();

        $this->assertCount(1, $class->called);
        $this->assertEquals('DefaultDatabaseSeeder', $class->called[0][1]['--seeder']);
    }
}

class DummyTestCase
{
    use MigratesDatabases;

    public $called = [];
    public $app = [];

    public function __construct()
    {
        $this->app[Kernel::class] = new Fluent();
    }

    public function artisan(...$args)
    {
        $this->called[] = $args;
    }
}
