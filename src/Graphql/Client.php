<?php

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

    public function request(string $query, array $variables = [])
    {
        $response = Http::withToken($this->token)
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
