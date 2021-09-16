<?php

namespace Butler\Service\Tests;

use Butler\Health\Check;
use Butler\Health\Result;

class TestCheck extends Check
{
    public string $description = 'A test check';

    public function run(): Result
    {
        return Result::ok('Looking good.');
    }
}
