<?php

declare(strict_types=1);

namespace Butler\Service\Auth;

use Illuminate\Auth\GenericUser;

class SessionUser extends GenericUser
{
    public static function store(array $attributes): static
    {
        session(['butler-user' => $attributes]);

        return new static($attributes);
    }

    public static function retrieve(): ?static
    {
        if ($sessionData = session('butler-user')) {
            return new static($sessionData);
        }

        return null;
    }
}
