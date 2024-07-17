<?php

declare(strict_types=1);

namespace Butler\Service\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class SessionUserProvider implements UserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        return $identifier ? SessionUser::retrieve() : null;
    }

    public function retrieveByToken($identifier, $token) {}

    public function updateRememberToken(Authenticatable $user, $token) {}

    public function retrieveByCredentials(array $credentials) {}

    public function validateCredentials(Authenticatable $user, array $credentials) {}

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false) {}
}
