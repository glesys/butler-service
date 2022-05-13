<?php

namespace Tests\Database;

use Butler\Service\Database\DatabaseManager;
use Butler\Service\Tests\TestCase;

class DatabaseManagerTest extends TestCase
{
    public function test_forget()
    {
        $databaseManager = new class extends DatabaseManager {
            public function __construct()
            {
                $this->connections = [
                    'db1' => true,
                    'db2' => true,
                ];
            }
        };

        $databaseManager->forget('db2');

        $this->assertEquals(['db1' => true], $databaseManager->getConnections());
    }
}
