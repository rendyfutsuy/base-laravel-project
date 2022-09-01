<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
            'api.profile.password',
            'api.profile.update',
            'api.user.store',
            'api.user.update',
            'api.user.destroy',
            'api.user.show',
            'api.user.index',
            'api.staff.store',
            'api.staff.update',
            'api.staff.destroy',
            'api.staff.show',
            'api.staff.index',
            'api.superadmin.store',
            'api.superadmin.update',
            'api.superadmin.destroy',
            'api.superadmin.show',
            'api.superadmin.index',
            'api.permission.store',
            'api.permission.update',
            'api.permission.destroy',
            'api.permission.show',
            'api.permission.index',
            'api.permission.resync',
            'api.role.store',
            'api.role.update',
            'api.role.destroy',
            'api.role.show',
            'api.role.index',
            'api.role.permission.sync',
            'api.role.user.resync',
        ];

        $staffPermissionAPI = [
            'api.profile.password',
            'api.profile.update',
            'api.user.store',
            'api.user.update',
            'api.user.destroy',
            'api.user.show',
            'api.user.index',
        ];

        $normalUserPermissionAPI = [
            'api.profile.password',
            'api.profile.update',
        ];

        Permission::create(['name' => 'api.permission.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.permission.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.permission.resync', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.permission.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.permission.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.permission.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.role.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.role.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.role.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.role.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.role.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.role.permission.sync', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.role.user.resync', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.profile.password', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.profile.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.staff.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.staff.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.staff.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.staff.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.staff.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.superadmin.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.superadmin.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.superadmin.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.superadmin.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.superadmin.index', 'guard_name' => 'api']);

        Role::findByName('NORMAL_USER', 'api')->syncPermissions($normalUserPermissionAPI);

        Role::findByName('STAFF', 'api')->syncPermissions($staffPermissionAPI);

        Role::findByName('SUPER_ADMIN', 'api')->syncPermissions($adminPermissionAPI);
    }
}
