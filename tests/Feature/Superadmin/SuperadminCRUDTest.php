<?php

namespace Tests\Feature\Superadmin;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\Feature\Components\AuthCase;

class SuperadminCRUDTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function superadmin_can_store_new_superadmin()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'. random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_store_new_superadmin()
    {
        $currentUser = $this->login('staff@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'. random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_superadmin_can_not_store_new_superadmin()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->postJson(route('api.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'. Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_store_new_superadmin()
    {        
        $response = $this->postJson(route('api.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'. Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_update_new_superadmin()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('STAFF', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
        ->orderBy('id', 'DESC')->first()->id;
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->putJson(route('api.superadmin.update', $id), [
            'name' => 'My Name',
            'email' => 'my.name.'. random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_update_new_superadmin()
    {
        $currentUser = $this->login('staff@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->putJson(route('api.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'. random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_superadmin_can_not_update_new_superadmin()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->putJson(route('api.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'. Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_update_new_superadmin()
    {        
        $response = $this->putJson(route('api.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'. Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);
        
        $response->assertUnauthorized();
    }
        
    /** @test */
    public function superadmin_can_destroy_new_superadmin()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        $id = User::whereHas('roles', function ($roles) {
            $role = Role::findByName('STAFF', 'api');
            $roles->where('id', $role->id);
        })->whereNotIn('id', [$currentUser['user']['id']])
        ->orderBy('id', 'DESC')->first()->id;
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->deleteJson(route('api.superadmin.destroy', $id));
        
        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_destroy_new_superadmin()
    {
        $currentUser = $this->login('staff@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->deleteJson(route('api.superadmin.destroy', 1));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_superadmin_can_not_destroy_new_superadmin()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->deleteJson(route('api.superadmin.destroy', 1));
        
        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_destroy_new_superadmin()
    {        
        $response = $this->deleteJson(route('api.superadmin.destroy', 1));
        
        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_see_index()
    {
        $currentUser = $this->login('superadmin@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->getJson(route('api.superadmin.index'));
        
        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_see_index()
    {
        $currentUser = $this->login('staff@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->getJson(route('api.superadmin.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_superadmin_can_not_see_index()
    {
        $currentUser = $this->login('user.1@mailinator.com');
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $currentUser['token']
        ])->getJson(route('api.superadmin.index'));
        
        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_see_index()
    {        
        $response = $this->getJson(route('api.superadmin.index'));
        
        $response->assertUnauthorized();
    }
}
