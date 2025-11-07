<?php

namespace Tests\Feature\Components;

use Mockery;
use App\Models\User;

trait AuthCase
{
    use MockAuthHelper;

    /**
     * Get authentication token using mocked user.
     * This method now uses mocking instead of database queries.
     *
     * @return array
     */
    public function getAuthenticate(string $username, string $password)
    {
        if (request()->hasSession()) {
            request()->session()->flush();
        }

        // Mock user authentication instead of querying database
        $user = $this->mockUser($username, $username, 'user-id-'.md5($username));
        $token = $this->mockToken('mock-access-token-'.md5($username));

        $user->shouldReceive('createToken')
            ->with(Mockery::any())
            ->andReturn($token);

        return [
            'access_token' => $token->accessToken,
        ];
    }

    /**
     * Login using mocked user.
     * This method now uses mocking instead of database queries.
     *
     * @param  string|null  $email
     * @return array
     */
    protected function login($email = null)
    {
        if (! $email) {
            $this->markTestSkipped('Email is required for login. Please provide a valid email address.');

            return [
                'user' => null,
                'token' => null,
            ];
        }

        // Mock user instead of querying database
        $user = $this->mockUser($email, 'Test User', 'user-id-'.md5($email));
        $token = $this->mockToken('mock-access-token-'.md5($email));

        $user->shouldReceive('createToken')
            ->with(Mockery::any())
            ->andReturn($token);

        return [
            'user' => $user,
            'token' => $token->accessToken,
        ];
    }
}
