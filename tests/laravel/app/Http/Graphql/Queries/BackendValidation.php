<?php

declare(strict_types=1);

namespace App\Http\Graphql\Queries;

use Butler\Service\Graphql\Exceptions\BackendValidation as BackendValidationException;

class BackendValidation
{
    public function __invoke($root, $args, $context)
    {
        throw new BackendValidationException('validation-exception');
    }
}
