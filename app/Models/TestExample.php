<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Standardization\BaseModel;

class TestExample extends BaseModel
{
    protected $table = 'test_examples';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    const ACTIVATED = 2;

    const PENDING = 1;

    const REJECTED = 3;

    const EXPIRED = 4;

    protected $fillable = [
        'id',
        'name',
        'status',
        'created_at',
        'updated_at',
        'joined_at',
    ];

    protected static function booted()
    {
        parent::booted();

        static::created(function ($example) {
            if (! $example->joined_at) {
                $example->joined_at = Carbon::now();
                $example->save();
            }

            if (! $example->status) {
                $example->status = self::PENDING;
                $example->save();
            }
        });
    }

    public function getJoinedAtAttribute($value): ?string
    {
        if ($value == null) {
            return null;
        }

        return Carbon::parse($value)->format('Y/m/d H:i:s');
    }

    public function getJoinedTimestampAttribute(): ?Carbon
    {
        if ($this->joined_at == null) {
            return null;
        }

        return Carbon::parse($this->joined_at);
    }

    public function getStatusTextAttribute(): string
    {
        if ($this->status == self::ACTIVATED) {
            return __('status.active');
        }

        if ($this->status == self::REJECTED) {
            return __('status.rejected');
        }

        if ($this->status == self::EXPIRED) {
            return __('status.expired');
        }

        if ($this->status == self::PENDING) {
            return __('status.pending');
        }

        return __('status.unknown');
    }
}
