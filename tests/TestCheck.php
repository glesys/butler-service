<?php

namespace Butler\Service\Tests;

use Butler\Service\Health\Check;
use Butler\Service\Health\Result;

class TestCheck extends Check
{
    public string $description = 'A test check';

    public function run(): Result
    {
        return Result::ok('Looking good.');
    }
}
