<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_returns_html()
    {
        $this->get(route('health'))
            ->assertOk()
            ->assertSee(config('app.name'))
            ->assertSee('Test Check')
            ->assertSee('Looking good.');
    }

    public function test_returns_json()
    {
        $this->getJson(route('health'))
            ->assertOk()
            ->assertJsonPath('service.name', config('app.name'))
            ->assertJsonCount(4, 'checks')
            ->assertJsonStructure([
                'service' => [
                    'name',
                    'php',
                    'laravel',
                    'timezone',
                ],
                'checks' => [
                    [
                        'name',
                        'slug',
                        'group',
                        'description',
                        'result' => [
                            'message',
                            'state',
                            'value',
                        ],
                    ],
                ],
            ]);
    }
}
