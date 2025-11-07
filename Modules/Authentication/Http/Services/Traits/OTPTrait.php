<?php

namespace Modules\Authentication\Http\Services\Traits;

use Modules\Authentication\Models\OTP;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;

trait OTPTrait
{
    /**
     * get OTP data based on otp token given
     *
     * @param  string  $otpToken
     * @return \Modules\RAMv1\Models\OTP|false
     */
    public function getOTPbyToken($otpToken)
    {
        // Try to use OTPContract if available (for dependency injection)
        if (property_exists($this, 'otpRepository') && $this->otpRepository instanceof OTPContract) {
            return $this->otpRepository->getByToken($otpToken);
        }

        // Fallback to direct query (for backward compatibility)
        $otp = OTP::where('otp_token', $otpToken)
            ->where('is_active', true)
            ->first();

        if (! $otp) {
            return false;
        }

        return $otp;
    }
}
