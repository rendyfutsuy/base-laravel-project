<?php

namespace Modules\Hierarchy\Tests\Feature\Permissions;

use Tests\TestCase;
use App\Models\User;
use Modules\Hierarchy\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Components\AuthCase;

class ResyncPermissionToUserTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function users_is_required()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = Permission::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync.to.users', $permission->id), [
            'users' => [],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function users_is_must_be_array()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = Permission::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync.to.users', $permission->id), [
            'users' => 'not-array',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function users_is_must_be_exists()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = Permission::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync.to.users', $permission->id), [
            'users' => [
                'not-exists-id',
            ],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function permission_can_sync_personally_to_users()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $permission = Permission::query()
            ->where('name', 'api.user-management.staff.index')
            ->first();

        $user = User::create([
            'email' => 'trial.user.that.only.have.permissions@mailinator.com',
            'password' => Hash::make('12345'),
            'name' => 'Rendy Anggara',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.permission.resync.to.users', $permission->id), [
            'users' => [
                $user->id,
            ],
        ]);

        $response->assertOk();
    }

    /** @test */
    public function trial_user_can_access_permissable_api()
    {
        $currentUser = $this->login('trial.user.that.only.have.permissions@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.staff.index'));

        $response->assertOk();
    }
}
