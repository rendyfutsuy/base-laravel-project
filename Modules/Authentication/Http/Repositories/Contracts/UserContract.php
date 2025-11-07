<?php

namespace Modules\Authentication\Http\Repositories\Contracts;

use App\Models\User;

interface UserContract
{
    public function paginated();

    public function validateUserRole(User $user): bool;

    public function find($id): User;
}
