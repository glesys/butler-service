<?php

namespace Butler\Service\Graphql;

use Illuminate\Support\Facades\Http;

class Client
{
    private $url;
    private $token;
    private $timeout;

    public function __construct(string $url, string $token, int $timeout = 5)
    {
        $this->url = $url;
        $this->token = $token;
        $this->timeout = $timeout;
    }

    public function request(string $query, array $variables = [])
    {
        $response = Http::withToken($this->token)
            ->timeout($this->timeout)
            ->post($this->url, compact('query', 'variables'))
            ->throw();

        if (isset($response['data'])) {
            return $response->json();
        }

        throw new \Exception($response->body());
    }
}
