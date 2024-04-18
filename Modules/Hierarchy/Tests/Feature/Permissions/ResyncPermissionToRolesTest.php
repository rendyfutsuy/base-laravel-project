<?php

namespace Modules\Hierarchy\Tests\Feature\Permissions;

use Tests\TestCase;
use Modules\Hierarchy\Models\Role;
use Modules\Hierarchy\Models\Permission;
use Tests\Feature\Components\AuthCase;

class ResyncPermissionToRolesTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function permissions_in_permission_sync_is_required()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = Permission::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync', $permission->id), [
            'roles' => [],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function permissions_in_permission_sync_is_must_exists()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = Permission::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync', $permission->id), [
            'roles' => ['try', 'my', 'logic', 'here'],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function superadmin_can_resync_permission()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = clone Permission::findByName('api.hierarchy.role.index', 'api');
        $testRole = Role::where('name', 'STAFF')->first();
        $roles = $permission->roles->pluck('id')->toArray();
        $before = count($roles);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync', $permission->id), [
            'roles' => array_merge($roles, [$testRole->id]),
        ]);

        $response->assertOk();

        $after = Permission::findByName('api.hierarchy.role.index', 'api')->roles->count();
        $this->assertNotEquals($before, $after);

        $this->assertTrue($testRole->hasPermissionTo($permission, 'api'));
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
