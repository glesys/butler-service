<?php

declare(strict_types=1);

namespace App\Http\Graphql\Queries;

class Long
{
    public function __invoke($root, $args, $context)
    {
        return $args['value'];
    }
}
