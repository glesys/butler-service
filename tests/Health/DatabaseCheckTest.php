<?php

namespace Butler\Service\Tests\Health;

use Butler\Service\Health\Checks\Database;
use Butler\Service\Health\Result;
use Butler\Service\Tests\TestCase;

class DatabaseCheckTest extends TestCase
{
    public function test_unknown_when_no_database_connections_exist()
    {
        config(['database.connections' => []]);

        $result = (new Database())->run();

        $this->assertEquals('No database connections found.', $result->message);
        $this->assertEquals(Result::UNKNOWN, $result->state);
        $this->assertNull($result->value());
    }

    public function test_ok_when_database_connection_succeeds()
    {
        config(['database.connections' => [
            'testing' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ]
        ]]);

        $result = (new Database())->run();

        $this->assertEquals('Connected to all databases.', $result->message);
        $this->assertEquals(Result::OK, $result->state);
        $this->assertEquals(1, $result->value());
    }

    public function test_critical_when_no_database_connection_fails()
    {
        config(['database.connections' => ['foobar']]);

        $result = (new Database())->run();

        $this->assertEquals('Connected to 0 of 1 databases.', $result->message);
        $this->assertEquals(Result::CRITICAL, $result->state);
        $this->assertEquals(0, $result->value());
    }
}
