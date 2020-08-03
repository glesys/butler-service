<?php

namespace Butler\Service\Testing\Concerns;

use Butler\Auth\JwtUser;

trait InteractsWithAuthentication
{
    public function actingAsJwtUser(array $data = []): self
    {
        return $this->actingAs(new JwtUser($data));
    }
}
