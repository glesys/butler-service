<?php

namespace Butler\Service\Models;

use Butler\Service\Database\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Sanctum\HasApiTokens;

class Consumer extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use HasApiTokens;
}
