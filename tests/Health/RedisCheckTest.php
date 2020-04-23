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
            return $this->assertTrue(true);
        }

        $result = (new Redis())->run();

        $this->assertEquals('Redis extension not enabled.', $result->message);
        $this->assertEquals(Result::UNKNOWN, $result->status);
        $this->assertNull($result->value());
    }
}
