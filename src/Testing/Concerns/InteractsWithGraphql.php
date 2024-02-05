<?php

declare(strict_types=1);

namespace Butler\Service\Testing\Concerns;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Assert;

trait InteractsWithGraphql
{
    public function assertGraphqlSent(
        ?callable $callback = null,
        ?string $query = null,
        array $variables = [],
    ): static {
        /** @var array{0:Request,1:Response} */
        foreach (Http::recorded() as $pair) {
            [$request, $response] = $pair;

            if (
                $request->method() === 'POST' &&
                is_graphql($request['query'] ?? '') &&
                (! $callback || $callback($request, $response)) &&
                (! $query || $query === $request['query']) &&
                (! $variables || $variables === $request['variables'] ?? [])
            ) {
                Assert::assertTrue(true);

                return $this;
            }
        }

        $this->fail('An expected GraphQL request was not recorded.');
    }
}
