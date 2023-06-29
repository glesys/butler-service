<?php

declare(strict_types=1);

namespace Butler\Service\Testing\Concerns;

use Butler\Auth\ButlerAuth;
use Butler\Service\Auth\SessionUser;
use Butler\Service\Models\Consumer;

trait InteractsWithAuthentication
{
    public function actingAsUser(array $data = [], $guard = null)
    {
        $user = new SessionUser(array_merge([
            'id' => rand(1, 999),
            'username' => 'username',
            'name' => 'name',
            'email' => 'user@example.com',
            'oauth_token' => 'token',
            'oauth_refresh_token' => 'refresh-token',
            'remember_token' => null,
        ], $data));

        return $this->actingAs($user, $guard);
    }

    public function actingAsConsumer(array $data = [], $abilities = ['*']): self
    {
        ButlerAuth::actingAs(new Consumer($data), $abilities);

        return $this;
    }
}
