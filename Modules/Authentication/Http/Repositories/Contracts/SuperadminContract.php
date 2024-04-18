<?php

namespace Modules\Authentication\Http\Repositories\Contracts;

use Modules\Authentication\Models\User;

interface SuperadminContract
{
    public function paginated();

    public function validateUserRole(User $user): bool;
}
