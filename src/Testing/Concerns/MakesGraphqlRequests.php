<?php

declare(strict_types=1);

namespace Butler\Service\Testing\Concerns;

use Illuminate\Testing\TestResponse;

trait MakesGraphqlRequests
{
    public function graphql(string $query, array $variables = []): TestResponse
    {
        return $this->postJson(route('graphql'), compact('query', 'variables'));
    }
}
