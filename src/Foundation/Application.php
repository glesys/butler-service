<?php

namespace Butler\Service\Foundation;

use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{
    public function configPath($path = '')
    {
        return realpath(__DIR__ . '/../config');
    }
}
