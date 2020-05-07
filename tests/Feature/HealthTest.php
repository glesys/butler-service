<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_it_returns_json()
    {
        $this->getJson(route('health'))
            ->assertOk()
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
            ])
            ->assertJsonPath('service.name', config('app.name'))
            ->assertJsonCount(4, 'checks');
    }
}
