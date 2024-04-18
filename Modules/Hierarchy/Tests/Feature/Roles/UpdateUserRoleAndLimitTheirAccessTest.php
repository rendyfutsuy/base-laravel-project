<?php

namespace Modules\Hierarchy\Tests\Feature\Roles;

use Tests\TestCase;
use App\Models\User;
use Modules\Hierarchy\Models\Role;
use Tests\Feature\Components\AuthCase;

class UpdateUserRoleAndLimitTheirAccessTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function staff_user_still_can_access_all_registered_feature()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('STAFF', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
            ->orderBy('id', 'DESC')->first()->id;

        // user index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.user.index'))->assertOk();

        // store user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // show user detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.user.show', $id), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // edit user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.user.update', $id), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // destroy user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.user.destroy', $id))->assertOk();
    }

    /** @test */
    public function super_admin_change_staff_user_roles_ad__unvalidate_d__user()
    {
        $role = Role::findByName('UNVALIDATED_USER', 'api');
        $currentUser = User::where('email', 'staff@mailinator.com')->first();
        $login = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$login['token'],
        ])->postJson(route('api.hierarchy.role.user.resync', [
            'user' => $currentUser->id,
            'role' => $role->id,
        ]));

        $response->assertOk();
    }

    /** @test */
    public function staff_now_can_not_access_all_staff_registered_feature()
    {
        $currentUser = $this->login('staff@mailinator.com');

        // user index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.user.index'))->assertForbidden();

        // store user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show user detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.user.show', 1), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.user.update', 1), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.user.destroy', 1))->assertForbidden();

        // superadmin index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.superadmin.index'))->assertForbidden();

        // store superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'superadmin.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show superadmin detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.superadmin.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.superadmin.destroy', 1))->assertForbidden();

        // staff index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.staff.index'))->assertForbidden();

        // store staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.staff.store'), [
            'name' => 'My Name',
            'email' => 'staff.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show staff detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.staff.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.staff.destroy', 1))->assertForbidden();

        // role index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.hierarchy.role.index'))->assertForbidden();

        // store role
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.role.store'), [
            'name' => 'My Name',
        ])->assertForbidden();

        // show role detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.hierarchy.role.show', 1), [
            'name' => 'My Name',
        ])->assertForbidden();

        $role = Role::findByName('STAFF', 'api');
        // sync role
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.hierarchy.role.permission.sync', $role->id), [
            'permissions' => [],
        ])->assertForbidden();
    }

    /** @test */
    public function super_admin_change_back_staff_user_roles_to__staff()
    {
        $role = Role::findByName('STAFF', 'api');
        $currentUser = User::where('email', 'staff@mailinator.com')->first();
        $login = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$login['token'],
        ])->postJson(route('api.hierarchy.role.user.resync', [
            'user' => $currentUser->id,
            'role' => $role->id,
        ]));

        $response->assertOk();
    }

    /** @test */
    public function staff_user_can_access_all_registered_feature_again()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('STAFF', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
            ->orderBy('id', 'DESC')->first()->id;

        // user index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.user.index'))->assertOk();

        // store user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // show user detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.user.show', $id), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // edit user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.user.update', $id), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // destroy user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.user.destroy', $id))->assertOk();
    }
}
