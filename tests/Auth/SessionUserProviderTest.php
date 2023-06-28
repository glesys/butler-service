<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Auth;

use Butler\Service\Auth\SessionUser;
use Butler\Service\Auth\SessionUserProvider;
use Butler\Service\Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;

class SessionUserProviderTest extends TestCase
{
    public function test_retrieveById()
    {
        $this->assertNull((new SessionUserProvider())->retrieveById('some identifier'));

        SessionUser::store(['id' => 1]);

        $this->assertNull((new SessionUserProvider())->retrieveById(''));

        $this->assertInstanceOf(
            Authenticatable::class,
            (new SessionUserProvider())->retrieveById('some identifier')
        );
    }
}
