<?php

namespace Butler\Service\Models;

use Butler\Service\Database\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthAuthenticatable;

class Consumer extends Model implements AuthAuthenticatable
{
    use Authenticatable;
}
