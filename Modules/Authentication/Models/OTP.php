<?php

namespace Modules\Authentication\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    use HasFactory;

    protected $table = 'otps';

    protected $fillable = [
        'user_id',
        'code',
        'otp_token',
        'is_active',
        'expired_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
