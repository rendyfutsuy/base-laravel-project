<?php

namespace Tests\Feature\Roles;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Event;
use Tests\Feature\Components\AuthCase;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SyncPermissionByRoleTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function permissions_in_permission_sync_is_required()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.role.permission.sync', 1), [
            'permissions' => [],
        ]);
               
        $response->assertStatus(400);
    }

    /** @test */
    public function permissions_in_permission_sync_is_must_be_array_numeric()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $role = Role::find(1);
        $permissions = $role->permissions->pluck('name');

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.role.permission.sync', $role->id), [
            'permissions' => $permissions,
        ]);
               
        $response->assertStatus(400);
    }

    /** @test */
    public function role_must_be_exists()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.role.permission.sync', 9999), [
            'permissions' => [],
        ]);
               
        $response->assertNotFound();
    }

    /** @test */
    public function superadmin_sync_role_index_to_staff_role()
    {
        $role = Role::find(2);
        $permissions = $role->permissions->pluck('id')->toArray();
        $testPermission = Permission::findByName('api.role.index', 'api');

        $currentUser = $this->login('superadmin@mailinator.com');
        $before = count($permissions);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.role.permission.sync', $role->id), [
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
            "Authorization" => "Bearer " . $currentUser['token']
        ])->getJson(route('api.role.index'));

        $response->assertOk();

        $role = Role::find(2);
        $role->revokePermissionTo('api.role.index');

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->getJson(route('api.role.index'));
            
        $response->assertForbidden();
    }
}
