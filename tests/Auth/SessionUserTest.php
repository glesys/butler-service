<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Auth;

use Butler\Service\Auth\SessionUser;
use Butler\Service\Tests\TestCase;

class SessionUserTest extends TestCase
{
    public function test_store()
    {
        $user = SessionUser::store(['id' => 10]);

        $this->assertInstanceOf(SessionUser::class, $user);
        $this->assertEquals(['id' => 10], session('butler-user'));
    }

    public function test_retrieve()
    {
        $this->assertNull(SessionUser::retrieve());

        session(['butler-user' => ['id' => 20]]);

        $user = SessionUser::retrieve();

        $this->assertInstanceOf(SessionUser::class, $user);
        $this->assertEquals(20, $user->id);
    }
}
