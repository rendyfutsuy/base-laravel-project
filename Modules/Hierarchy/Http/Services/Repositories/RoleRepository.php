<?php

namespace Modules\Hierarchy\Http\Services\Repositories;

use App\Models\User;
use Modules\Hierarchy\Models\Role;
use Modules\Hierarchy\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use App\Http\Repositories\BaseRepository;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\RoleContract;

class RoleRepository extends BaseRepository implements RoleContract
{
    /** @var Role */
    protected $role;

    public function __construct(Role $role)
    {
        parent::__construct($role);
        $this->role = $role;
    }

    public function store(array $attributes): Model
    {
        return $this->role->create([
            'name' => $attributes['name'],
            'guard_name' => 'api',
        ]);
    }

    public function sync($permissions, Role $role): Role
    {
        $permissions = Permission::whereIn('id', $permissions)->get();
        $role->syncPermissions($permissions);

        return $role->load('permissions')->refresh();
    }

    public function resync(User $user, Role $role): User
    {
        $user->syncRoles($role);

        return $user->load('roles');
    }
}
