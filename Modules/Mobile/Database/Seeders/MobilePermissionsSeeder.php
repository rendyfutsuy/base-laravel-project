<?php

namespace Modules\Mobile\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hierarchy\Models\Role;
use Modules\Hierarchy\Models\Permission;

class MobilePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminPermissionAPI = [
            'api.authentication.mobile.profile.index',
            'api.authentication.mobile.profile.password',
            'api.authentication.mobile.profile.update',
            'api.user-management.mobile.user.store',
            'api.user-management.mobile.user.update',
            'api.user-management.mobile.user.destroy',
            'api.user-management.mobile.user.show',
            'api.user-management.mobile.user.index',
            'api.user-management.mobile.staff.store',
            'api.user-management.mobile.staff.update',
            'api.user-management.mobile.staff.destroy',
            'api.user-management.mobile.staff.show',
            'api.user-management.mobile.staff.index',
            'api.user-management.mobile.superadmin.store',
            'api.user-management.mobile.superadmin.update',
            'api.user-management.mobile.superadmin.destroy',
            'api.user-management.mobile.superadmin.show',
            'api.user-management.mobile.superadmin.index',
            'api.hierarchy.mobile.permission.store',
            'api.hierarchy.mobile.permission.update',
            'api.hierarchy.mobile.permission.destroy',
            'api.hierarchy.mobile.permission.show',
            'api.hierarchy.mobile.permission.index',
            'api.hierarchy.mobile.permission.resync',
            'api.hierarchy.mobile.permission.resync.to.users',
            'api.hierarchy.mobile.role.store',
            'api.hierarchy.mobile.role.update',
            'api.hierarchy.mobile.role.destroy',
            'api.hierarchy.mobile.role.show',
            'api.hierarchy.mobile.role.index',
            'api.hierarchy.mobile.role.permission.sync',
            'api.hierarchy.mobile.role.user.resync',
        ];

        $staffPermissionAPI = [
            'api.authentication.mobile.profile.index',
            'api.authentication.mobile.profile.password',
            'api.authentication.mobile.profile.update',
            'api.user-management.mobile.user.store',
            'api.user-management.mobile.user.update',
            'api.user-management.mobile.user.destroy',
            'api.user-management.mobile.user.show',
            'api.user-management.mobile.user.index',
        ];

        $normalUserPermissionAPI = [
            'api.authentication.mobile.profile.index',
            'api.authentication.mobile.profile.password',
            'api.authentication.mobile.profile.update',
        ];

        Permission::create(['name' => 'api.hierarchy.mobile.permission.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.permission.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.permission.resync', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.permission.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.permission.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.permission.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.role.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.role.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.role.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.role.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.role.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.role.permission.sync', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.permission.resync.to.users', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.hierarchy.mobile.role.user.resync', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.authentication.mobile.profile.password', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.authentication.mobile.profile.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.authentication.mobile.profile.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.user.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.user.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.user.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.user.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.user.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.staff.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.staff.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.staff.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.staff.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.staff.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.superadmin.store', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.superadmin.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.superadmin.destroy', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.superadmin.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'api.user-management.mobile.superadmin.index', 'guard_name' => 'api']);

        // sync All Permission for Normal User Mobile Permissions
        $normalUser = Role::findByName('NORMAL_USER', 'api');

        $normalUserPermissions = $normalUser->permissions->pluck('name')->toArray();

        $normalUserPermissionAPI = array_merge($normalUserPermissionAPI, $normalUserPermissions);

        $normalUser->syncPermissions($normalUserPermissionAPI);

        //sync All Permission for Staff User Mobile Permissions
        $staffUser = Role::findByName('STAFF', 'api');

        $staffUserPermissions = $staffUser->permissions->pluck('name')->toArray();

        $staffPermissionAPI = array_merge($staffPermissionAPI, $staffUserPermissions);

        $staffUser->syncPermissions($staffPermissionAPI);

        //sync All Permission for Super Admin User Mobile Permissions
        $superAdminUser = Role::findByName('SUPER_ADMIN', 'api');

        $superAdminUserPermissions = $superAdminUser->permissions->pluck('name')->toArray();

        $adminPermissionAPI = array_merge($adminPermissionAPI, $superAdminUserPermissions);

        $superAdminUser->syncPermissions($adminPermissionAPI);
    }
}
