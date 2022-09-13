<?php

namespace Tests\Feature\Roles;

use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Components\AuthCase;

class RoleCRUDTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function superadmin_can_store_new_role()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.store'), [
            'name' => 'EXAMPLE',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_store_new_role()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.store'), [
            'name' => 'EXAMPLE',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_role_can_not_store_new_role()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.store'), [
            'name' => 'EXAMPLE',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_store_new_role()
    {
        $response = $this->postJson(route('api.role.store'), [
            'name' => 'EXAMPLE',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_see_index()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $role = Role::orderBy('id', 'DESC')->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.role.index'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_see_index()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.role.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_role_can_not_see_index()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.role.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.role.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_sync_new_permission()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $role = Role::find(1);
        $permissions = $role->permissions->pluck('id')->toArray();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.permission.sync', $role->id), [
            'permissions' => $permissions,
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_sync_new_permission()
    {
        $currentUser = $this->login('staff@mailinator.com');
        $role = Role::find(1);
        $permissions = $role->permissions->pluck('id');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.permission.sync', $role->id), [
            'permissions' => $permissions,
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_permission_can_not_sync_new_permission()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        $role = Role::find(1);
        $permissions = $role->permissions->pluck('id');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.permission.sync', $role->id), [
            'permissions' => $permissions,
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_sync_new_permission()
    {
        $role = Role::find(1);
        $permissions = $role->permissions->pluck('id');

        $response = $this->postJson(route('api.role.permission.sync', $role->id), [
            'permissions' => $permissions,
        ]);

        $response->assertUnauthorized();
    }
}
