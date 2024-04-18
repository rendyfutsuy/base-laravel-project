<?php

namespace Modules\Notification\Models;

use App\Models\Standardization\BaseModel;

class Notification extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'sent_at',
        'received_back_at',
        'pushed_at',
        'is_read',
        'notifiable_type',
        'notifiable_id',
        'payload',
        'type',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'payload' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }
}
