<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
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
        ])->postJson(route('api.staff.store'), [
            'name' => 'My Name',
            'email' => 'staff.test.1@mailinator.com',
            'password' => '12345',
        ]);
        $response->assertOk();
    }

    /** @test */
    public function staff_user_can_access_all_registered_feature()
    {
        $currentUser = $this->login('staff.test.1@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('STAFF', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
        ->orderBy('id', 'DESC')->first()->id;

        // user index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user.index'))->assertOk();

        // store user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // show user detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user.show', $id), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // edit user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user.update', $id), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // destroy user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user.destroy', $id))->assertOk();
    }

    /** @test */
    public function staff_user_can_no_access_all_unregistered_feature()
    {
        $currentUser = $this->login('staff.test.1@mailinator.com');

        // superadmin index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.superadmin.index'))->assertForbidden();

        // store superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'superadmin.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show superadmin detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.superadmin.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.superadmin.destroy', 1))->assertForbidden();

        // staff index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.staff.index'))->assertForbidden();

        // store staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.staff.store'), [
            'name' => 'My Name',
            'email' => 'staff.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show staff detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.staff.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.staff.destroy', 1))->assertForbidden();

        // role index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.role.index'))->assertForbidden();

        // store role
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.store'), [
            'name' => 'My Name',
        ])->assertForbidden();

        // show role detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.role.show', 1), [
            'name' => 'My Name',
        ])->assertForbidden();

        // sync role
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.permission.sync', 1), [
            'permissions' => [],
        ])->assertForbidden();
    }

    /** @test */
    public function superadmin_can_store_new_user()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user.store'), [
            'name' => 'My Name',
            'email' => 'user.test.1@mailinator.com',
            'password' => '12345',
        ]);
        $response->assertOk();
    }

    /** @test */
    public function user_can_no_access_all_unregistered_feature()
    {
        $currentUser = $this->login('user.test.1@mailinator.com');

        // user index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user.index'))->assertForbidden();

        // store user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show user detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.user.show', 1), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.user.update', 1), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy user
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.user.destroy', 1))->assertForbidden();

        // superadmin index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.superadmin.index'))->assertForbidden();

        // store superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'superadmin.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show superadmin detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.superadmin.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy superadmin
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.superadmin.destroy', 1))->assertForbidden();

        // staff index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.staff.index'))->assertForbidden();

        // store staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.staff.store'), [
            'name' => 'My Name',
            'email' => 'staff.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show staff detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.staff.show', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // edit staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy staff
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->deleteJson(route('api.staff.destroy', 1))->assertForbidden();

        // role index
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.role.index'))->assertForbidden();

        // store role
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.store'), [
            'name' => 'My Name',
        ])->assertForbidden();

        // show role detail
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.role.show', 1), [
            'name' => 'My Name',
        ])->assertForbidden();

        // sync role
        $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.role.permission.sync', 1), [
            'permissions' => [],
        ])->assertForbidden();
    }
}
