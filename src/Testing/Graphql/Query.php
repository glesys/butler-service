<?php

declare(strict_types=1);

namespace Butler\Service\Testing\Graphql;

use Closure;

class Query
{
    public int $httpResponseStatus = 200;
    public mixed $httpResponseBody = ['data' => 'payload'];

    public mixed $expectedResult = 'payload';
    public mixed $expectedResultOnException = null;

    public ?string $expectedQuery = null;
    public array $expectedVariables = [];

    public bool $shouldHandleException = true;

    public static function test(Closure $callback): static
    {
        return new static($callback);
    }

    public function __construct(public readonly Closure $callback) {}

    public function returns(mixed $expectedResult): static
    {
        $this->expectedResult = $expectedResult;

        return $this;
    }

    public function returnsOnException(mixed $expectedResultOnException): static
    {
        $this->expectedResultOnException = $expectedResultOnException;

        return $this;
    }

    public function sends(string $expectedQuery, array $expectedVariables = []): static
    {
        throw_unless(is_graphql($expectedQuery), 'Expected query is not a valid GraphQL query.');

        $this->expectedQuery = $expectedQuery;
        $this->expectedVariables = $expectedVariables;

        return $this;
    }

    public function receives(mixed $httpResponseBody, ?int $httpResponseStatus = null): static
    {
        $this->httpResponseBody = $httpResponseBody;

        if ($httpResponseStatus) {
            $this->httpResponseStatus = $httpResponseStatus;
        }

        return $this;
    }

    public function throwsExceptionOnErrors(): static
    {
        $this->shouldHandleException = false;

        return $this;
    }

    public function run(): mixed
    {
        return ($this->callback)();
    }
}
