<?php

declare(strict_types=1);

namespace Butler\Service\Database;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    use HasFactory;

    protected $connection = 'default';

    protected static $unguarded = true;
}
