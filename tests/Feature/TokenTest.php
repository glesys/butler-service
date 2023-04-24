<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Models\Consumer;
use Butler\Service\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TokenTest extends TestCase
{
    use DatabaseMigrations;

    public function test_index_as_guest()
    {
        $this->get(route('tokens.index'))->assertRedirectToRoute('home');
    }

    public function test_index_can_return_view()
    {
        $this->actingAsUser()
            ->withoutVite()
            ->get(route('tokens.index'))
            ->assertOk()
            ->assertHeader('cache-control', 'no-store, private')
            ->assertViewIs('butler::tokens');
    }

    public function test_index_can_returns_json()
    {
        Consumer::create(['name' => 'consumer1'])->createToken();
        Consumer::create(['name' => 'consumer2'])->createToken();

        $this->actingAsUser()
            ->getJson(route('tokens.index'))
            ->assertOk()
            ->assertJsonPath('*.owner', ['consumer1', 'consumer2']);
    }

    public function test_store_as_guest()
    {
        $this->post(route('tokens.store'))->assertRedirectToRoute('home');
    }

    public function test_store_as_user()
    {
        $data = [
            'consumer' => 'consumer@example.com',
            'abilities' => ['*'],
            'name' => null,
        ];

        $this->actingAsUser()
            ->post(route('tokens.store'), $data)
            ->assertJsonStructure(['accessToken', 'plainTextToken']);
    }

    public function test_destroy_as_guest()
    {
        $this->post(route('tokens.delete'))->assertRedirectToRoute('home');
    }

    public function test_destroy_as_user()
    {
        $consumer = Consumer::create(['name' => 'consumer']);

        $tokenId = $consumer->createToken()->accessToken->id;

        $this->actingAsUser()
            ->delete(route('tokens.delete'), ['ids' => [$tokenId]])
            ->assertNoContent();

        $this->assertTrue($consumer->tokens->isEmpty());
    }
}
