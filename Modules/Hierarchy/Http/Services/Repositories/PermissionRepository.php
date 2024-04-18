<?php

namespace Modules\Hierarchy\Http\Services\Repositories;

use App\Models\User;
use Modules\Hierarchy\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Modules\Hierarchy\Models\Permission;
use App\Http\Repositories\BaseRepository;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\PermissionContract;

class PermissionRepository extends BaseRepository implements PermissionContract
{
    /** @var Permission */
    protected $permission;

    public function __construct(Permission $permission)
    {
        parent::__construct($permission);
        $this->permission = $permission;
    }

    public function store(array $attributes): Model
    {
        $permission = $this->permission->create([
            'name' => $attributes['name'],
            'guard_name' => 'api',
        ]);

        if (! array_key_exists('roles', $attributes)) {
            return $permission->load('roles');
        }

        if ($attributes['roles'] == null || $attributes['roles'] == []) {
            return $permission->load('roles');
        }

        $roles = Role::whereIn('id', $attributes['roles'])->get();
        $permission->syncRoles($roles);

        return $permission->load('roles');
    }

    public function resync($roles, Permission $permission): Permission
    {
        $roles = Role::whereIn('id', $roles)->get();
        $permission->syncRoles($roles);

        return $permission->refresh()->load('roles');
    }

    public function resyncToUser(User $user, Permission $permission): Permission
    {
        $permissions = $user->permissions->pluck('name')->toArray();

        $user->syncPermissions(array_merge(
            $permissions, [$permission->name]
        ));

        return $permission->refresh();
    }
}
