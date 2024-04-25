<?php

namespace Modules\Mobile\Tests\Feature\UserManagement\Staff;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use Modules\Hierarchy\Models\Role;
use Tests\Feature\Components\AuthCase;

class StaffCRUDTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function superadmin_can_store_new_staff()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_store_new_staff()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_staff_can_not_store_new_staff()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_store_new_staff()
    {
        $response = $this->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_update_new_staff()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('STAFF', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
            ->orderBy('id', 'DESC')->first()->id;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.mobile.staff.update', $id), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_update_new_staff()
    {
        $user = User::whereHas('roles', function ($roles) {
            $roles->where('name', 'STAFF');
        })->first();

        $currentUser = $this->login($user->email);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.mobile.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_staff_can_not_update_new_staff()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.mobile.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_update_new_staff()
    {
        $response = $this->putJson(route('api.user-management.mobile.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_destroy_new_staff()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('STAFF', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
            ->orderBy('id', 'DESC')->first()->id;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.mobile.staff.destroy', $id));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_destroy_new_staff()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.mobile.staff.destroy', 1));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_staff_can_not_destroy_new_staff()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.mobile.staff.destroy', 1));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_destroy_new_staff()
    {
        $response = $this->deleteJson(route('api.user-management.mobile.staff.destroy', 1));

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_see_index()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.staff.index'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_see_index()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.staff.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_staff_can_not_see_index()
    {
        $currentUser = $this->login('user.1@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.staff.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.user-management.mobile.staff.index'));

        $response->assertUnauthorized();
    }
}
