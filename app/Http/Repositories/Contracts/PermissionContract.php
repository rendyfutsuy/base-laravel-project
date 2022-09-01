<?php

namespace App\Http\Repositories\Contracts;

use Spatie\Permission\Models\Permission;

interface PermissionContract
{
	public function resync($roles = [], Permission $permission): Permission;
	public function paginated();
}