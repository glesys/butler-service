<?php

declare(strict_types=1);

namespace App\Http\Graphql\Mutations;

class Start
{
    public function __invoke($root, $args, $context)
    {
        return 'started';
    }
}
