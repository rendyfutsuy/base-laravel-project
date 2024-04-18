<?php

namespace Modules\Hierarchy\Http\Services\Repositories\Contracts;

use Modules\Hierarchy\Models\Permission;

interface PermissionContract
{
    public function resync($roles, Permission $permission): Permission;

    public function paginated();
}
