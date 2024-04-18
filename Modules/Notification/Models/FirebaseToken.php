<?php

namespace Modules\Notification\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FirebaseToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $device_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken whereUserId($value)
 *
 * @mixin \Eloquent
 *
 * @property string $user_agent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FirebaseToken whereUserAgent($value)
 */
class FirebaseToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'user_agent',

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
