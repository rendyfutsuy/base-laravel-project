<?php

namespace App\Http\Repositories\Contracts;

use App\Models\User;

interface SuperadminContract
{
    public function paginated();

    public function validateUserRole(User $user): bool;
}
