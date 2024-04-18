<?php

namespace Modules\Hierarchy\Tests\Feature\Roles;

use Tests\TestCase;
use Modules\Hierarchy\Models\Role;
use Tests\Feature\Components\AuthCase;
use Modules\Hierarchy\Models\Permission;

class SyncPermissionByRoleTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function permissions_in_permission_sync_is_required()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $role = Role::where('name', 'SUPER_ADMIN')->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.role.permission.sync', $role->id), [
            'permissions' => [],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function permissions_in_permission_sync_is_must_be_array_numeric()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $role = Role::where('name', 'SUPER_ADMIN')->first();
        $permissions = $role->permissions->pluck('name');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.role.permission.sync', $role->id), [
            'permissions' => $permissions,
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function role_must_be_exists()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.role.permission.sync', 9999), [
            'permissions' => [],
        ]);

        $response->assertNotFound();
    }

    /** @test */
    public function superadmin_sync_role_index_to_staff_role()
    {
        $role = Role::where('name', 'STAFF')->first();
        $permissions = $role->permissions->pluck('id')->toArray();
        $testPermission = Permission::findByName('api.hierarchy.role.index', 'api');

        $currentUser = $this->login('superadmin@mailinator.com');
        $before = count($permissions);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.role.permission.sync', $role->id), [
            'permissions' => array_merge($permissions, [$testPermission->id]),
        ]);

        $after = $role->refresh()->permissions->pluck('id')->count();

        $this->assertNotEquals($before, $after);

        $response->assertOk();
    }

    /** @test */
    public function staff_role_now_can_access_role_index_after_edit()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.hierarchy.role.index'));

        $response->assertOk();

        $role = Role::where('name', 'STAFF')->first();
        $permission = Permission::where('name', 'api.hierarchy.role.index')->first();
        $role->permissions()->detach($permission->id);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.hierarchy.role.index'));

        $response->assertForbidden();
    }
}
