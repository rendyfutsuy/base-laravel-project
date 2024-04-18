<?php

namespace Modules\Authentication\Http\Services\Traits;

use Modules\Authentication\Models\OTP;

trait OTPTrait
{
    /**
     * get OTP data based on otp token given
     *
     * @param  string  $otpToken
     * @return \Modules\RAMv1\Models\OTP
     */
    public function getOTPbyToken($otpToken)
    {
        $otp = OTP::where('otp_token', $otpToken)
            ->where('is_active', true)
            ->first();

        if (! $otp) {
            return false;
        }

        return $otp;
    }
}
