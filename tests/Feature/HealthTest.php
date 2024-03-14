<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class HealthTest extends TestCase
{
    public function test_it_can_return_view()
    {
        $this->withoutVite()
            ->get(route('health'))
            ->assertOk()
            ->assertHeader('cache-control', 'no-store, private')
            ->assertViewIs('butler::health.index');
    }

    public function test_it_can_return_json()
    {
        $this->getJson(route('health'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->hasAll('about', 'checks')->whereAllType([
                    'about.butlerService.version' => 'string',
                    'about.laravelOctane.version' => 'string',
                    'about.laravelOctane.running' => 'boolean',
                ])
            );
    }
}
