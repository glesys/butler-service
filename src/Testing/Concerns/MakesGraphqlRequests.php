<?php

declare(strict_types=1);

namespace Butler\Service\Testing\Concerns;

trait MakesGraphqlRequests
{
    public function graphql(string $query, array $variables = [])
    {
        return $this->postJson(route('graphql'), compact('query', 'variables'));
    }
}
