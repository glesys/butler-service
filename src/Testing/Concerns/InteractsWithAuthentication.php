<?php

namespace Butler\Service\Testing\Concerns;

use Butler\Service\Models\Consumer;
use Laravel\Sanctum\Sanctum;

trait InteractsWithAuthentication
{
    public function actingAsConsumer(array $data = [], $abilities = ['*']): self
    {
        Sanctum::actingAs(new Consumer($data), $abilities);

        return $this;
    }
}
