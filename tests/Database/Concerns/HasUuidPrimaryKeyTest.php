<?php

namespace Butler\Service\Tests\Database\Concerns;

use Butler\Service\Tests\TestCase;
use Butler\Service\Database\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class HasUuidPrimaryKeyTest extends TestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new class extends Model {
            use HasUuidPrimaryKey;

            public function fireCreatingEvent()
            {
                return $this->fireModelEvent('creating');
            }
        };
    }

    public function test_creating_event_sets_id_correctly()
    {
        $this->model->fireCreatingEvent();

        $this->assertMatchesRegularExpression('/^[a-f0-9-]{36}$/', $this->model->id);
    }

    public function test_creating_event_does_not_set_id_when_already_set()
    {
        $this->model->id = 'foobar';
        $this->model->fireCreatingEvent();

        $this->assertEquals('foobar', $this->model->id);
    }

    public function test_getIncrementing()
    {
        $this->assertFalse($this->model->getIncrementing());
    }

    public function test_getKeyType()
    {
        $this->assertEquals('string', $this->model->getKeyType());
    }
}
