<?php

namespace Butler\Service\Testing\Concerns;

use Butler\Auth\ButlerAuth;
use Butler\Service\Models\Consumer;
use Illuminate\Auth\GenericUser;

trait InteractsWithAuthentication
{
    public function actingAsUser(array $data = [], $guard = null)
    {
        return $this->be(new GenericUser($data), $guard);
    }

    public function actingAsConsumer(array $data = [], $abilities = ['*']): self
    {
        ButlerAuth::actingAs(new Consumer($data), $abilities);

        return $this;
    }
}
