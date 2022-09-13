<?php

namespace App\Http\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use App\Http\Repositories\Contracts\RoleContract;

class RoleRepository extends BaseRepository implements RoleContract
{
    /** @var Role */
    protected $role;

    public function __construct(Role $role)
    {
        parent::__construct($role);
        $this->role = $role;
    }

    /**
     * @param  array  $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        return $this->role->create([
            'name' => $attributes['name'],
            'guard_name' => 'api',
        ]);
    }

    public function paginated()
    {
        return $this->role->query()->paginate(request('per_page', 10));
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
