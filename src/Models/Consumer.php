<?php

declare(strict_types=1);

namespace Butler\Service\Models;

use Butler\Auth\Concerns\HasAccessTokens;
use Butler\Auth\Contracts\HasAccessTokens as HasAccessTokensContract;
use Butler\Service\Database\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Consumer extends Model implements AuthenticatableContract, HasAccessTokensContract
{
    use Authenticatable;
    use HasAccessTokens;
}
