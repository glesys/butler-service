<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Feature;

use Butler\Service\Tests\TestCase;
use Illuminate\Session\NullSessionHandler;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class UpTest extends TestCase
{
    public function test_it_returns_ok()
    {
        $this->get('/up')->assertOk();
    }

    public function test_it_ignores_the_host_name()
    {
        $this->get('http://localhost:8080/up')->assertOk();
    }

    public function test_it_returns_ok_when_the_session_store_is_down()
    {
        Session::extend('down', fn () => new class extends NullSessionHandler
        {
            public function read($sessionId): string
            {
                throw new RuntimeException('Session store is down');
            }
        });
        config(['session.driver' => 'down']);

        $this->get('/')->assertServerError();
        $this->get('/up')->assertOk();
    }
}
