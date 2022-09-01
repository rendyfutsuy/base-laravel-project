<?php

namespace App\Http\Repositories\Contracts;

use App\Models\User;
use Spatie\Permission\Models\Role;

interface RoleContract
{
	public function sync($permissions = [], Role $role): Role;
	public function paginated();
	public function resync(User $user, Role $role): User;
}