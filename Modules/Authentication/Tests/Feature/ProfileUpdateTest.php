<?php

namespace Modules\Authentication\Tests\Feature;

use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Laravel\Passport\Passport;
use Tests\Feature\Components\MockAuthHelper;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use MockAuthHelper;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function superadmin_can_update_their_profile()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.authentication.profile.update']
        );

        // Use Passport::actingAs for authentication
        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.authentication.profile.update'), [
            'name' => 'My Name',
            'email' => 'superadmin@mailinator.com',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function superadmin_can_update_their_password()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.authentication.profile.password']
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.authentication.profile.password'), [
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function superadmin_can_see_their_profile()
    {
        $userMock = $this->mockUser('superadmin@mailinator.com', 'Superadmin', 'user-id-123');

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.authentication.profile.index'));

        $response->assertOk();
    }

    #[Test]
    public function user_can_update_their_profile()
    {
        $userMock = $this->mockUser(
            'user@mailinator.com',
            'User',
            'user-id-456',
            ['api.authentication.profile.update']
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.authentication.profile.update'), [
            'name' => 'My Name',
            'email' => 'user@mailinator.com',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function user_can_update_their_password()
    {
        $userMock = $this->mockUser(
            'user@mailinator.com',
            'User',
            'user-id-456',
            ['api.authentication.profile.password']
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.authentication.profile.password'), [
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function user_can_see_their_profile()
    {
        $userMock = $this->mockUser('user@mailinator.com', 'User', 'user-id-456');

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.authentication.profile.index'));

        $response->assertOk();
    }

    #[Test]
    public function staff_can_update_their_profile()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-789',
            ['api.authentication.profile.update']
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.authentication.profile.update'), [
            'name' => 'My Name',
            'email' => 'staff@mailinator.com',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_can_update_their_password()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-789',
            ['api.authentication.profile.password']
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.authentication.profile.password'), [
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_can_see_their_profile()
    {
        $userMock = $this->mockUser('staff@mailinator.com', 'Staff', 'user-id-789');

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.authentication.profile.index'));

        $response->assertOk();
    }
}
