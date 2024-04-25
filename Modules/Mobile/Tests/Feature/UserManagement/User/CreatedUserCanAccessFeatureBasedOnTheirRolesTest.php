<?php

namespace Modules\Mobile\Tests\Feature\UserManagement\User;

use Tests\TestCase;
use App\Models\User;
use Modules\Hierarchy\Models\Role;
use Tests\Feature\Components\AuthCase;

class CreatedUserCanAccessFeatureBasedOnTheirRolesTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function superadmin_can_store_staff_user()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'staff.test.1.mobile@mailinator.com',
            'password' => '12345',
        ]);
        $response->assertOk();
    }

    /** @test */
    public function staff_user_can_access_all_registered_feature()
    {
        $currentUser = $this->login('staff.test.1.mobile@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('STAFF', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
            ->orderBy('id', 'DESC')->first()->id;

        // user index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.user.index'))->assertOk();

        // store user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // show user detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.user.show', $id), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // edit user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.mobile.user.update', $id), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // destroy user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.mobile.user.destroy', $id))->assertOk();
    }

    /** @test */
    public function staff_user_can_no_access_all_unregistered_feature()
    {
        $currentUser = $this->login('staff.test.1.mobile@mailinator.com');

        // superadmin index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.superadmin.index'))->assertForbidden();

        // store superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'superadmin.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show superadmin detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.superadmin.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.mobile.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.mobile.superadmin.destroy', 1))->assertForbidden();

        // staff index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.staff.index'))->assertForbidden();

        // store staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'staff.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show staff detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.staff.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.mobile.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.mobile.staff.destroy', 1))->assertForbidden();

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
    public function superadmin_can_store_new_user()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.user.store'), [
            'name' => 'My Name',
            'email' => 'user.test.1.mobile@mailinator.com',
            'password' => '12345',
        ]);
        $response->assertOk();
    }

    /** @test */
    public function user_can_no_access_all_unregistered_feature()
    {
        $currentUser = $this->login('user.test.1.mobile@mailinator.com');

        // user index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.user.index'))->assertForbidden();

        // store user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show user detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.user.show', 1), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.mobile.user.update', 1), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.mobile.user.destroy', 1))->assertForbidden();

        // superadmin index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.superadmin.index'))->assertForbidden();

        // store superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'superadmin.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show superadmin detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.superadmin.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.mobile.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.mobile.superadmin.destroy', 1))->assertForbidden();

        // staff index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.staff.index'))->assertForbidden();

        // store staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'staff.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show staff detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user-management.mobile.staff.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user-management.mobile.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user-management.mobile.staff.destroy', 1))->assertForbidden();

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
}
