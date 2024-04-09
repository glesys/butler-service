<?php

namespace Butler\Service\Graphql\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;

class BackendValidation extends Exception implements ClientAware
{
    public function isClientSafe()
    {
        return true;
    }

    public function getCategory()
    {
        return 'backend-validation';
    }
}
