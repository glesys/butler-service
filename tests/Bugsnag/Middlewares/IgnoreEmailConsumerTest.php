<?php

namespace Butler\Service\Tests\Bugsnag\Middlewares;

use Bugsnag\Report;
use Butler\Service\Bugsnag\Middlewares\IgnoreEmailConsumer;
use Butler\Service\Tests\TestCase;
use Exception;

class IgnoreEmailConsumerTest extends TestCase
{
    public function test_false_is_returned_for_consumer_with_email_as_name()
    {
        $report = $this->mock(Report::class, function ($mock) {
            $mock->expects()->getUser()->andReturns([
                'name' => 'foo@bar.baz',
            ]);
        });

        $this->assertFalse((new IgnoreEmailConsumer())($report));
    }

    public function test_nothing_is_returned_for_consumer_without_email_as_name()
    {
        $report = $this->mock(Report::class, function ($mock) {
            $mock->expects()->getUser()->andReturns([
                'name' => 'foobar',
            ]);
        });

        $this->assertEmpty((new IgnoreEmailConsumer())($report));
    }

    public function test_nothing_is_returned_if_name_is_undefined()
    {
        $report = $this->mock(Report::class, function ($mock) {
            $mock->expects()->getUser()->andReturns([]);
        });

        $this->assertEmpty((new IgnoreEmailConsumer())($report));
    }

    public function test_nothing_is_returned_if_exception_is_thrown()
    {
        $report = $this->mock(Report::class, function ($mock) {
            $mock->expects()->getUser()->andThrow(new Exception('error'));
        });

        $this->assertEmpty((new IgnoreEmailConsumer())($report));
    }
}
