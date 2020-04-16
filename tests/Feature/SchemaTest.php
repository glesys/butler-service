<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;

class SchemaTest extends TestCase
{
    public function test_happy_path()
    {
        $this->get(route('schema'))
            ->assertOk()
            ->assertSee('type Query {');
    }
}
