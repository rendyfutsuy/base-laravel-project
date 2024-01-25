<?php

namespace App\Http\Repositories\Contracts;

use App\Models\Hierarchy\Permission;

interface PermissionContract
{
    public function resync($roles, Permission $permission): Permission;

    public function paginated();
}
