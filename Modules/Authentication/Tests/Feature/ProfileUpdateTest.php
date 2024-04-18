<?php

namespace Tests\Feature\Profile;

use Tests\Feature\Components\AuthCase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function superadmin_can_update_their_profile()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.authentication.profile.update'), [
            'name' => 'My Name',
            'email' => 'superadmin@mailinator.com',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function superadmin_can_update_their_password()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.authentication.profile.password'), [
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function superadmin_can_see_their_profile()
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.authentication.profile.index'));

        $response->assertOk();
    }

    /** @test */
    public function user_can_update_their_profile()
    {
        $currentUser = $this->login('user@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.authentication.profile.update'), [
            'name' => 'My Name',
            'email' => 'user@mailinator.com',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function user_can_update_their_password()
    {
        $currentUser = $this->login('user@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.authentication.profile.password'), [
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function user_can_see_their_profile()
    {
        $currentUser = $this->login('user@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.authentication.profile.index'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_update_their_profile()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->putJson(route('api.authentication.profile.update'), [
            'name' => 'My Name',
            'email' => 'staff@mailinator.com',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_update_their_password()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->postJson(route('api.authentication.profile.password'), [
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_see_their_profile()
    {
        $currentUser = $this->login('staff@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.authentication.profile.index'));

        $response->assertOk();
    }
}
