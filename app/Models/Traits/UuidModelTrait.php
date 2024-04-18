<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait UuidModelTrait
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->setKeyType('string');
            $model->setIncrementing(false);
            $model->setAttribute($model->getKeyName(), Str::uuid());
        });
    }
}
