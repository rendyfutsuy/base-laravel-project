<?php

namespace Modules\Hierarchy\Http\Services\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Hierarchy\Models\Role;

interface RoleContract
{
    public function sync($permissions, Role $role): Role;

    public function paginated();

    public function resync(User $user, Role $role): User;

    public function getPermissionsByIds(array $permissionIds): Collection;
}
