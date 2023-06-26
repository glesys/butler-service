<?php

declare(strict_types=1);

namespace Butler\Service\Graphql;

use Illuminate\Support\Facades\Http;

class Client
{
    public function __construct(
        private string $url,
        private string $token,
        private int $timeout = 10,
    ) {
    }

    public static function fromArray(array $array): static
    {
        return isset($array['timeout'])
            ? new static($array['url'], $array['token'], $array['timeout'])
            : new static($array['url'], $array['token']);
    }

    public static function fromConfig(string $configKey): static
    {
        return static::fromArray(config($configKey));
    }

    public function request(string $query, array $variables = [])
    {
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->withCorrelation()
            ->timeout($this->timeout)
            ->post($this->url, compact('query', 'variables'))
            ->throw();

        if (isset($response['data'])) {
            return $response->json();
        }

        throw new \Exception($response->body());
    }
}
