<?php

namespace Modules\Authentication\Http\Repositories\Contracts;

interface OTPContract
{
    /**
     * Generate code OTP or Update if Exist
     *
     * @param  int  $userId
     * @return \Modules\RAMv1\Models\OTP
     */
    public function generate($userId);

    /**
     * Revoke OTP if Exist
     *
     * @param  int  $userId
     * @param  string  $code
     */
    public function revoke($userId, $code);

    /**
     * Get OTP by User ID and Code
     *
     * @param  int  $userId
     * @param  string  $code
     * @return \Modules\RAMv1\Models\OTP
     */
    public function getActive($userId, $code = null);
}
