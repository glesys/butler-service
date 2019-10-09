<?php

namespace Butler\Service\Database;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    protected $connection = 'default';

    protected $guarded = [];
}
