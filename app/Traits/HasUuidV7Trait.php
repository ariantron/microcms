<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait HasUuidV7Trait
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function bootHasUuidV7Trait(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid7()->toString();
            }
        });
    }
}
