<?php

namespace Butler\Service\Testing\Concerns;

use Butler\Service\Models\Consumer;

trait InteractsWithAuthentication
{
    public function actingAsConsumer(array $data = []): self
    {
        return $this->actingAs(new Consumer($data));
    }
}
