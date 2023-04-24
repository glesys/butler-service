<?php

declare(strict_types=1);

namespace App;

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
