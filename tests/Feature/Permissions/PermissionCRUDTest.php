<?php

namespace Tests\Feature\Permissions;

use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Components\AuthCase;
use Spatie\Permission\Models\Permission;

class PermissionCRUDTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function superadmin_can_store_new_permission()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.permission.store'), [
            'name' => 'api.example.store',
            'roles' => [Role::find(1)->id],
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_store_new_permission()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.permission.store'), [
            'name' => 'api.example.store',
            'roles' => [Role::find(1)->id],
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_permission_can_not_store_new_permission()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.permission.store'), [
            'name' => 'api.example.store',
            'roles' => [Role::find(1)->id],
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_store_new_permission()
    {
        $response = $this->postJson(route('api.permission.store'), [
            'name' => 'api.example.store',
            'roles' => [Role::find(1)->id],
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_see_index()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.permission.index'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_see_index()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.permission.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_permission_can_not_see_index()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.permission.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.permission.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_resync_permission()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = Permission::find(15);
        $roles = $permission->roles->pluck('id')->toArray();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.permission.resync', $permission->id), [
            'roles' => $roles,
        ]);

        $response->assertOk();
    }

    /** @test */
    public function normal_permission_can_not_resync_permission()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        $permission = Permission::find(1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.permission.resync', $permission->id), [
            'roles' => [1, 2, 3],
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_resync_permission()
    {
        $permission = Permission::find(1);

        $response = $this->postJson(route('api.permission.resync', $permission->id), [
            'roles' => [1, 2, 3],
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function staff_can_not_resync_permission()
    {
        $currentUser = $this->login('staff@mailinator.com');
        $permission = Permission::find(1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.permission.resync', $permission->id), [
            'roles' => [1, 2, 3],
        ]);

        $response->assertForbidden();
    }
}
