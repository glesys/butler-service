<?php

namespace Butler\Service\Testing\Concerns;

use Butler\Auth\ButlerAuth;
use Butler\Service\Models\Consumer;

trait InteractsWithAuthentication
{
    public function actingAsConsumer(array $data = [], $abilities = ['*']): self
    {
        ButlerAuth::actingAs(new Consumer($data), $abilities);

        return $this;
    }
}
