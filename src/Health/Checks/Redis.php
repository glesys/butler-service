<?php

namespace Butler\Service\Health\Checks;

use Butler\Service\Health\Check;
use Butler\Service\Health\Result;
use Illuminate\Support\Facades\Redis as RedisClient;
use Illuminate\Support\Str;

class Redis extends Check
{
    public string $group = 'core';
    public string $description = 'Check redis connection.';

    public function run(): Result
    {
        if (! extension_loaded('redis')) {
            return Result::unknown('Redis extension not enabled.');
        }

        try {
            RedisClient::set(
                $key = 'butler-service-health-check',
                $string = Str::random()
            );

            if (RedisClient::get($key) === $string) {
                return Result::ok('Connected to redis on <host>.');
            }
        } catch (\Exception $_) {
            //
        }

        return Result::critical('Could not connect to redis on <host>.');
    }
}
