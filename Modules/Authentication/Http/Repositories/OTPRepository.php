<?php

namespace Modules\Authentication\Http\Repositories;

use Carbon\Carbon;
use Modules\Authentication\Models\OTP;
use App\Http\Repositories\BaseRepository;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;

class OTPRepository extends BaseRepository implements OTPContract
{
    public function __construct(OTP $otp)
    {
        parent::__construct($otp);
    }

    /**
     * Generate code OTP or Update if Exist
     *
     * @param  int  $userId
     * @return \Modules\RAMv1\Models\OTP
     */
    public function generate($userId)
    {
        $otp = $this->findByCriteria([
            'user_id' => $userId,
            'is_active' => true,
        ]);

        $code = rand(100000, 999999);

        if ($otp) {
            $this->update([
                'code' => $code,
                'expired_at' => now()->addMinutes(15),
            ], $otp->id);

            $otp = $this->find($otp->id);

            return $otp;
        }

        $otp = $this->store([
            'user_id' => $userId,
            'code' => $code,
            'is_active' => true,
            'otp_token' => bcrypt(Carbon::now()),
            'expired_at' => now()->addMinutes(15),
        ]);

        return $otp;
    }

    /**
     * Revoke OTP if Exist
     *
     * @param  int  $userId
     * @param  string  $code
     */
    public function revoke($userId, $code)
    {
        $otp = $this->findByCriteria([
            'user_id' => $userId,
            'code' => $code,
            'is_active' => true,
        ]);

        if (! $otp) {
            return;
        }

        $this->delete($otp->id);
    }

    /**
     * Get OTP by User ID and Code
     *
     * @param  int  $userId
     * @param  string  $code
     * @return \Modules\RAMv1\Models\OTP
     */
    public function getActive($userId, $code = null)
    {
        $columns = [
            'user_id' => $userId,
            'is_active' => true,
        ];

        if ($code) {
            $columns = array_merge(['code' => $code], $columns);
        }

        return $this->findByCriteria($columns);
    }
}
