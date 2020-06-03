<?php

namespace Butler\Service\Tests\Health;

use Butler\Service\Health\Checks\Redis;
use Butler\Service\Health\Result;
use Butler\Service\Tests\TestCase;

class RedisCheckTest extends TestCase
{
    public function test_unknown_when_redis_extension_is_not_loaded()
    {
        if (extension_loaded('redis')) {
            $this->markTestSkipped();
        }

        $result = (new Redis())->run();

        $this->assertEquals('Redis extension not enabled.', $result->message);
        $this->assertEquals(Result::UNKNOWN, $result->state);
        $this->assertNull($result->value());
    }

    public function test_unknown_when_redis_host_is_undefined()
    {
        if (! extension_loaded('redis')) {
            $this->markTestSkipped();
        }

        config(['database.redis.default.host' => null]);

        $result = (new Redis())->run();

        $this->assertEquals('Redis host undefined.', $result->message);
        $this->assertEquals(Result::UNKNOWN, $result->state);
        $this->assertNull($result->value());
    }
}
