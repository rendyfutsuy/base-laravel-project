<?php

namespace Tests\Feature\User;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\Feature\Components\AuthCase;

class UserCRUDTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function superadmin_can_store_new_user()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.user.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'. random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);
                
        $response->assertOk();
    }

    /** @test */
    public function staff_can_store_new_user()
    {
        $currentUser = $this->login('staff@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.user.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'. random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function normal_user_can_not_store_new_user()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.user.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'. Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_store_new_user()
    {        
        $response = $this->postJson(route('api.user.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'. Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_update_new_user()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('NORMAL_USER', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
        ->orderBy('id', 'DESC')->first()->id;
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->putJson(route('api.user.update', $id), [
            'name' => 'My Name',
            'email' => 'my.name.'. random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertOk();
    }

    /** @test */
    public function staff_can_update_new_user()
    {
        $currentUser = $this->login('staff@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('NORMAL_USER', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
        ->orderBy('id', 'DESC')->first()->id;
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->putJson(route('api.user.update', $id), [
            'name' => 'My Name',
            'email' => 'my.name.'. random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function normal_user_can_not_update_new_user()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->putJson(route('api.user.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'. Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_update_new_user()
    {        
        $response = $this->putJson(route('api.user.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'. Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertUnauthorized();
    }
        
    /** @test */
    public function superadmin_can_destroy_new_user()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('NORMAL_USER', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
        ->orderBy('id', 'DESC')->first()->id;
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->deleteJson(route('api.user.destroy', $id));
        
        $response->assertOk();
    }

    /** @test */
    public function staff_can_destroy_new_user()
    {
        $currentUser = $this->login('staff@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('NORMAL_USER', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
        ->orderBy('id', 'DESC')->first()->id;
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->deleteJson(route('api.user.destroy', $id));

        $response->assertOk();
    }

    /** @test */
    public function normal_user_can_not_destroy_new_user()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->deleteJson(route('api.user.destroy', 1));
        
        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_destroy_new_user()
    {        
        $response = $this->deleteJson(route('api.user.destroy', 1));
        
        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_see_index()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->getJson(route('api.user.index'));
        
        $response->assertOk();
    }

    /** @test */
    public function staff_can_see_index()
    {
        $currentUser = $this->login('staff@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->getJson(route('api.user.index'));

        $response->assertOk();
    }

    /** @test */
    public function normal_user_can_not_see_index()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->getJson(route('api.user.index'));
        
        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_see_index()
    {        
        $response = $this->getJson(route('api.user.index'));
        
        $response->assertUnauthorized();
    }
}
