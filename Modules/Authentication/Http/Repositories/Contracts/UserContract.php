<?php

namespace Modules\Authentication\Http\Repositories\Contracts;

use Modules\Authentication\Models\User;

interface UserContract
{
    public function paginated();

    public function validateUserRole(User $user): bool;
}
