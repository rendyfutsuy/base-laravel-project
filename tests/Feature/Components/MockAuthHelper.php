<?php

namespace Tests\Feature\Components;

use Mockery;
use App\Models\User;
use Laravel\Passport\Passport;

trait MockAuthHelper
{
    /**
     * Create a mock user for authentication.
     *
     * @return \Mockery\MockInterface
     */
    protected function mockUser(string $email = 'test@mailinator.com', string $name = 'Test User', string $id = 'user-id-123', array $permissions = [])
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = $id;
        $user->email = $email;
        $user->name = $name;

        // Mock basic attributes
        $user->shouldReceive('getAttribute')->with('id')->andReturn($id);
        $user->shouldReceive('getAttribute')->with('email')->andReturn($email);
        $user->shouldReceive('getAttribute')->with('name')->andReturn($name);

        // Mock roles and permissions for PermissionMiddleware
        $rolesCollection = collect([]);
        $permissionsCollection = collect([]);

        $user->shouldReceive('getAttribute')->with('roles')->andReturn($rolesCollection);
        $user->shouldReceive('getAttribute')->with('permissions')->andReturn($permissionsCollection);

        $user->roles = $rolesCollection;
        $user->permissions = $permissionsCollection;

        // Mock assigned_permissions attribute
        $user->shouldReceive('getAttribute')->with('assigned_permissions')->andReturn($permissions);
        $user->assigned_permissions = $permissions;

        // Mock fresh() method for PermissionMiddleware
        $user->shouldReceive('fresh')->andReturn($user);

        return $user;
    }

    /**
     * Create a mock token for authentication.
     *
     * @return \Mockery\MockInterface
     */
    protected function mockToken(string $accessToken = 'mock-access-token')
    {
        $token = Mockery::mock();
        $token->accessToken = $accessToken;
        $token->shouldReceive('getAttribute')->with('accessToken')->andReturn($accessToken);

        return $token;
    }

    /**
     * Mock authentication with user and token.
     */
    protected function mockAuth(string $email = 'test@mailinator.com', string $name = 'Test User', string $id = 'user-id-123'): array
    {
        $user = $this->mockUser($email, $name, $id);
        $token = $this->mockToken('mock-access-token-'.$id);

        // Mock Passport token creation
        $user->shouldReceive('createToken')
            ->with(Mockery::any())
            ->andReturn($token);

        return [
            'user' => $user,
            'token' => $token->accessToken,
        ];
    }

    /**
     * Mock authentication service response.
     */
    protected function mockAuthResponse(string $email, string $password, bool $valid = true): array
    {
        if (! $valid) {
            return [
                'status' => 400,
                'message' => 'Invalid credentials',
            ];
        }

        $user = $this->mockUser($email);
        $token = $this->mockToken('mock-access-token-'.$user->id);

        return [
            'status' => 200,
            'data' => [
                'access_token' => $token->accessToken,
                'refresh_token' => 'mock-refresh-token',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                ],
            ],
        ];
    }
}
