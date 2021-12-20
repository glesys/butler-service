<?php

namespace Butler\Service\Tests\Database;

use Butler\Service\Database\Model;
use Butler\Service\Tests\TestCase;

class ModelTest extends TestCase
{
    public function test_connection_name()
    {
        $model = new class extends Model {
            //
        };

        $this->assertEquals('default', $model->getConnectionName());
    }

    public function test_is_unguarded()
    {
        $model = new class extends Model {
            //
        };

        $this->assertTrue($model->isUnguarded());
    }
}
