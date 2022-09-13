<?php

namespace Tests\Feature\Permissions;

use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Components\AuthCase;
use Spatie\Permission\Models\Permission;

class ResyncPermissionToRolesTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function permissions_in_permission_sync_is_required()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.permission.resync', 1), [
            'roles' => [],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function permissions_in_permission_sync_is_must_be_array_numeric()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = Permission::find(1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.permission.resync', $permission->id), [
            'roles' => ['try', 'my', 'logic', 'here'],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function superadmin_can_resync_permission()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = clone Permission::findByName('api.role.index', 'api');
        $testRole = Role::find(2);
        $roles = $permission->roles->pluck('id')->toArray();
        $before = count($roles);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.permission.resync', $permission->id), [
            'roles' => array_merge($roles, [$testRole->id]),
        ]);

        $response->assertOk();

        $after = Permission::findByName('api.role.index', 'api')->roles->count();
        $this->assertNotEquals($before, $after);

        $this->assertTrue($testRole->hasPermissionTo($permission, 'api'));
    }

    /** @test */
    public function staff_role_now_can_access_role_index_after_edit()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.role.index'));

        $response->assertOk();

        $role = Role::find(2);
        $role->revokePermissionTo('api.role.index');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.role.index'));

        $response->assertForbidden();
    }
}
