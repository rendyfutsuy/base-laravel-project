<?php

namespace Modules\Hierarchy\Tests\Feature\Permissions;

use Tests\TestCase;
use Modules\Hierarchy\Models\Role;
use Modules\Hierarchy\Models\Permission;
use Tests\Feature\Components\AuthCase;

class PermissionCRUDTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function superadmin_can_store_new_permission()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.store'), [
            'name' => 'api.example.store',
            'roles' => [Role::where('name', 'SUPER_ADMIN')->first()->id],
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_store_new_permission()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.store'), [
            'name' => 'api.example.store',
            'roles' => [Role::where('name', 'SUPER_ADMIN')->first()->id],
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_permission_can_not_store_new_permission()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.store'), [
            'name' => 'api.example.store',
            'roles' => [Role::where('name', 'SUPER_ADMIN')->first()->id],
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_store_new_permission()
    {
        $response = $this->postJson(route('api.hierarchy.permission.store'), [
            'name' => 'api.example.store',
            'roles' => [Role::where('name', 'SUPER_ADMIN')->first()->id],
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_see_index()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.hierarchy.permission.index'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_see_index()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.hierarchy.permission.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_permission_can_not_see_index()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.hierarchy.permission.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.hierarchy.permission.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_resync_permission()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = Permission::first();
        $roles = $permission->roles->pluck('id')->toArray();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync', $permission->id), [
            'roles' => $roles,
        ]);

        $response->assertOk();
    }

    /** @test */
    public function normal_permission_can_not_resync_permission()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        $permission = Permission::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync', $permission->id), [
            'roles' => [1, 2, 3],
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_resync_permission()
    {
        $permission = Permission::first();

        $response = $this->postJson(route('api.hierarchy.permission.resync', $permission->id), [
            'roles' => [1, 2, 3],
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function staff_can_not_resync_permission()
    {
        $currentUser = $this->login('staff@mailinator.com');
        $permission = Permission::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync', $permission->id), [
            'roles' => [1, 2, 3],
        ]);

        $response->assertForbidden();
    }
}
