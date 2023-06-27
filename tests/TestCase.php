<?php

declare(strict_types=1);

namespace Butler\Service\Tests;

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function actingAsUser(array $data = [])
    {
        $user = new GenericUser(array_merge([
            'id' => rand(0, 999),
            'username' => 'username',
            'name' => 'name',
            'email' => 'user@example.com',
            'oauth_token' => 'token',
            'oauth_refresh_token' => 'refresh-token',
            'remember_token' => null,
        ], $data));

        return $this->actingAs($user, 'web');
    }
}
