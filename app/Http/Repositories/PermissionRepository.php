<?php

namespace App\Http\Repositories;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use App\Http\Repositories\Contracts\PermissionContract;

class PermissionRepository extends BaseRepository implements PermissionContract
{
    /** @var Permission */
    protected $permission;

    public function __construct(Permission $permission)
    {
        parent::__construct($permission);
        $this->permission = $permission;
    }

    /**
     * @param  array  $attributes
     * @return Model
     */
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

    public function paginated()
    {
        return $this->permission->query()->paginate(request('per_page', 10));
    }

    public function resync($roles, Permission $permission): Permission
    {
        $roles = Role::whereIn('id', $roles)->get();
        $permission->syncRoles($roles);

        return $permission->refresh()->load('roles');
    }
}
