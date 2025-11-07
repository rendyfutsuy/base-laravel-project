<?php

namespace Modules\Hierarchy\Http\Services\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Hierarchy\Models\Permission;

interface PermissionContract
{
    public function resync($roles, Permission $permission): Permission;

    public function paginated();

    public function resyncToUser(User $user, Permission $permission): Permission;

    public function getUsersByIds(array $userIds): Collection;

    public function getRolesByIds(array $roleIds): Collection;
}
