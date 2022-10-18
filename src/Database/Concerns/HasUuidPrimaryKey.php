<?php

declare(strict_types=1);

namespace Butler\Service\Database\Concerns;

trait HasUuidPrimaryKey
{
    protected static function bootHasUuidPrimaryKey()
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) str()->uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
