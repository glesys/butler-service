<?php

declare(strict_types=1);

namespace Butler\Service\Testing\Graphql;

use Butler\Service\Graphql\Service;
use Butler\Service\Testing\TestCase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use RuntimeException;

abstract class ServiceTestCase extends TestCase
{
    protected ?string $serviceClass = null;

    abstract public static function queryProvider();

    public function service(): Service
    {
        return new ($this->serviceClass ?? $this->guessServiceClass());
    }

    public function guessServiceClass(): string
    {
        $testClassName = str(basename((new ReflectionClass($this))->getFileName()))
            ->remove('Test.php');

        $this->serviceClass = class_exists('App\\Services\\' . $testClassName)
            ? 'App\\Services\\' . $testClassName
            : 'App\\' . $testClassName;

        return $this->serviceClass;
    }

    public function test_request_is_sent_correctly()
    {
        $service = $this->service();

        $this->assertNotEmpty($service->url);
        $this->assertNotEmpty($service->token);

        Http::preventStrayRequests()->fake([
            $this->service()->url => ['data' => ['__typename' => 'Query']],
        ]);

        $this->assertEquals(['__typename' => 'Query'], $service->request('{ __typename }'));

        $this->assertGraphqlSent(
            fn ($request) => $request->hasHeader('Authorization', "Bearer {$service->token}")
        );
    }

    #[DataProvider('queryProvider')]
    public function test_query_happy_path(Query $query)
    {
        Http::preventStrayRequests()->fake([
            $this->service()->url => Http::response(
                $query->httpResponseBody,
                $query->httpResponseStatus,
            ),
        ]);

        $result = $query->run();

        if (is_callable($query->expectedResult)) {
            $this->assertTrue(($query->expectedResult)($result));
        } else {
            $this->assertSame($query->expectedResult, $result);
        }

        if ($query->expectedQuery || $query->expectedVariables) {
            $this->assertGraphqlSent(
                query: $query->expectedQuery ?: null,
                variables: $query->expectedVariables ?: [],
            );
        }
    }

    #[DataProvider('queryProvider')]
    public function test_query_handles_graphql_error(Query $query): void
    {
        if (! $query->shouldHandleException) {
            $this->expectException(RuntimeException::class);
        }

        Http::preventStrayRequests()->fake([
            $this->service()->url => ['not-data' => []],
        ]);

        $result = $query->run();

        if ($result instanceof Collection) {
            $this->assertTrue($result->isEmpty());
        } else {
            $this->assertSame($query->expectedResultOnException, $result);
        }
    }

    #[DataProvider('queryProvider')]
    public function test_query_handles_http_server_error(Query $query): void
    {
        if (! $query->shouldHandleException) {
            $this->expectException(RequestException::class);
        }

        Http::preventStrayRequests()->fake([
            $this->service()->url => Http::response('Server error', 500),
        ]);

        $result = $query->run();

        if ($result instanceof Collection) {
            $this->assertTrue($result->isEmpty());
        } else {
            $this->assertSame($query->expectedResultOnException, $result);
        }
    }
}
