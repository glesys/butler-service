<?php

namespace Butler\Service\Health;

use Butler\Service\Health\Result;

abstract class Check
{
    public string $name;
    public string $slug;
    public string $group;
    public string $description;

    abstract public function run(): Result;
}
