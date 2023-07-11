<?php

declare(strict_types=1);

namespace Butler\Service\Graphql;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class Service
{
    public readonly string $url;
    public readonly string $token;

    protected int $timeout = 10;

    public function __construct()
    {
        $config = config(static::configKey());

        $this->url = $config['url'];
        $this->token = $config['token'];
    }

    public function request(string $query, array $variables = []): mixed
    {
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->withCorrelation()
            ->timeout($this->timeout)
            ->post($this->url, compact('query', 'variables'))
            ->throw();

        throw_unless(isset($response['data']), $response->body());

        return $response->json('data');
    }

    public function query(
        string $query,
        array $variables = [],
        string $key = null,
        mixed $default = null,
        bool $rescue = true,
    ): mixed {
        try {
            return data_get($this->request($query, $variables), $key, $default);
        } catch (Exception $exception) {
            throw_unless($rescue, $exception);

            Log::error($exception->getMessage(), compact('exception', 'variables'));
        }

        return $default;
    }

    public function collect(
        string $query,
        array $variables = [],
        string $key = '*.*',
        bool $rescue = true,
    ): Collection {
        return collect($this->query($query, $variables, $key, rescue: $rescue));
    }

    public static function configKey(): string
    {
        return str(class_basename(static::class))
            ->snake('-')
            ->prepend('services.')
            ->toString();
    }
}
