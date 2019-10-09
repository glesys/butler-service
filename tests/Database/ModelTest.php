<?php

namespace Butler\Service\Tests\Database;

use Butler\Service\Database\Model;
use Butler\Service\Tests\TestCase;

class ModelTest extends TestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new class extends Model {
            //
        };
    }

    public function test_connection_name()
    {
        $this->assertEquals('default', $this->model->getConnectionName());
    }

    public function test_is_unguarded()
    {
        $this->assertEquals([], $this->model->getGuarded());
    }
}
