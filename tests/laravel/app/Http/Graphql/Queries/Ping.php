<?php

declare(strict_types=1);

namespace App\Http\Graphql\Queries;

class Ping
{
    public function __invoke($root, $args, $context)
    {
        return 'pong';
    }
}
