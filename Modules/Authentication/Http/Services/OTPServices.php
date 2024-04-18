<?php

namespace Modules\Authentication\Http\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;

class OTPServices
{
    private $userRepository;

    private $otpRepository;

    public function __construct(OTPContract $OTPRepository, UserContract $userRepository)
    {
        $this->otpRepository = $OTPRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Revoke Previous Verification session and  verify user's email.
     *
     * @param  string  $code
     * @param  int  $userId
     */
    public function revokeAndUpdateEmailVerified($userId, $code)
    {
        return DB::transaction(function () use ($userId, $code) {
            $this->otpRepository->revoke(
                $userId,
                $code
            );

            $this->userRepository->update([
                'email_verified_at' => Carbon::now(),
            ], $userId);
        });
    }
}
