<?php

namespace Modules\Hierarchy\Http\Services\Repositories\Contracts;

use App\Models\User;
use Modules\Hierarchy\Models\Role;

interface RoleContract
{
    public function sync($permissions, Role $role): Role;

    public function paginated();

    public function resync(User $user, Role $role): User;
}
