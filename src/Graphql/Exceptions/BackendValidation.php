<?php

declare(strict_types=1);

namespace Butler\Service\Graphql\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;

class BackendValidation extends Exception implements ClientAware, ProvidesExtensions
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getExtensions(): array
    {
        return [
            'category' => 'backend-validation',
        ];
    }
}
