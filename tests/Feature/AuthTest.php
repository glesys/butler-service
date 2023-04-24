<?php

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class AuthTest extends TestCase
{
    public function test_redirect()
    {
        Socialite::shouldReceive('driver->redirect')
            ->andReturn(new RedirectResponse('http://localhost/sso'));

        $this->get(route('auth.redirect'))->assertRedirect('http://localhost/sso');
    }

    public function test_callback()
    {
        Socialite::shouldReceive('driver->user')->andReturn((object) [
            'id' => 1,
            'nickname' => 'nickname',
            'name' => 'name',
            'email' => 'user@example.com',
            'token' => 'token',
            'refreshToken' => 'refresh-token',
        ]);

        $this->get(route('auth.callback'))->assertRedirectToRoute('home');

        $this->assertAuthenticated();
    }

    public function test_logout_as_guest()
    {
        $this->post(route('auth.logout'))->assertRedirectToRoute('home');

        $this->assertGuest();
    }

    public function test_logout_as_user()
    {
        $this->actingAsUser();

        $this->post(route('auth.logout'))->assertRedirectToRoute('home');

        $this->assertGuest();
    }
}
