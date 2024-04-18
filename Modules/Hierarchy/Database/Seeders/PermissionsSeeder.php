<?php

namespace Modules\Hierarchy\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hierarchy\Models\Role;
use Modules\Hierarchy\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminPermissionAPI = [
            'api.authentication.profile.index',
            'api.authentication.profile.password',
            'api.authentication.profile.update',
            'api.user-management.user.store',
            'api.user-management.user.update',
            'api.user-management.user.destroy',
            'api.user-management.user.show',
            'api.user-management.user.index',
            'api.user-management.staff.store',
            'api.user-management.staff.update',
            'api.user-management.staff.destroy',
            'api.user-management.staff.show',
            'api.user-management.staff.index',
            'api.user-management.superadmin.store',
            'api.user-management.superadmin.update',
            'api.user-management.superadmin.destroy',
            'api.user-management.superadmin.show',
            'api.user-management.superadmin.index',
            'api.hierarchy.permission.store',
            'api.hierarchy.permission.update',
            'api.hierarchy.permission.destroy',
            'api.hierarchy.permission.show',
            'api.hierarchy.permission.index',
            'api.hierarchy.permission.resync',
            'api.hierarchy.permission.resync.to.users',
            'api.hierarchy.role.store',
            'api.hierarchy.role.update',
            'api.hierarchy.role.destroy',
            'api.hierarchy.role.show',
            'api.hierarchy.role.index',
            'api.hierarchy.role.permission.sync',
            'api.hierarchy.role.user.resync',
        ];

        $staffPermissionAPI = [
            'api.authentication.profile.index',
            'api.authentication.profile.password',
            'api.authentication.profile.update',
            'api.user-management.user.store',
            'api.user-management.user.update',
            'api.user-management.user.destroy',
            'api.user-management.user.show',
            'api.user-management.user.index',
        ];

        $normalUserPermissionAPI = [
            'api.authentication.profile.index',
            'api.authentication.profile.password',
            'api.authentication.profile.update',
        ];

        Permission::create(['name' => 'api.hierarchy.permission.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.permission.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.permission.resync', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.permission.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.permission.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.permission.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.role.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.role.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.role.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.role.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.role.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.role.permission.sync', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.permission.resync.to.users', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.role.user.resync', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.authentication.profile.password', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.authentication.profile.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.authentication.profile.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.user.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.user.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.user.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.user.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.user.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.staff.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.staff.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.staff.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.staff.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.staff.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.superadmin.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.superadmin.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.superadmin.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.superadmin.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.superadmin.index', 'guard_name' => 'api']);

        Role::findByName('NORMAL_USER', 'api')->syncPermissions($normalUserPermissionAPI);

        Role::findByName('STAFF', 'api')->syncPermissions($staffPermissionAPI);

        Role::findByName('SUPER_ADMIN', 'api')->syncPermissions($adminPermissionAPI);
    }
}
