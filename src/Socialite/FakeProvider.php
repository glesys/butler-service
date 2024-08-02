<?php

declare(strict_types=1);

namespace Butler\Service\Socialite;

class FakeProvider extends PassportProvider
{
    protected function getAuthUrl($state)
    {
        return route('auth.callback');
    }

    protected function getUserByToken($token)
    {
        return [
            'id' => 1000,
            'nickname' => 'Nickname',
            'name' => 'Name',
            'email' => 'user@butler.localhost',
            'avatar' => null,
        ];
    }

    protected function hasInvalidState()
    {
        return false;
    }

    public function getAccessTokenResponse($code)
    {
        return [
            'access_token' => 'abc123',
        ];
    }
}
