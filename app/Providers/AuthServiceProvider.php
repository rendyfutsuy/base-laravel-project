<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $tokenExpirationSecond = (int) config('auth.access_token_expiration_second');
        $refreshTokenExpirationDay = (int) config('auth.access_token_refresh_expiration_day');
        Passport::tokensExpireIn(now()->addSeconds($tokenExpirationSecond));
        Passport::refreshTokensExpireIn(now()->addDays($refreshTokenExpirationDay));
    }
}
