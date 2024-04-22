<?php

namespace Modules\Authentication\Http\Services\Traits;

use App\Models\User;

trait AuthBase
{
    protected function isEmailVerified(User $user)
    {
        return (bool) $user->email_verified_at;
    }

    protected function isActivated(User $user)
    {
        return (bool) $user->is_active;
    }

    protected function isAllAdditionalValidationPassed(User $user)
    {
        // insert additional Logic here
        return true;
    }
}
