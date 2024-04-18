<?php

namespace Modules\Authentication\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Authentication\Http\Repositories\OTPRepository;
use Modules\Authentication\Http\Repositories\UserRepository;
use Modules\Authentication\Http\Repositories\StaffRepository;
use Modules\Authentication\Http\Repositories\SuperadminRepository;
use Modules\Authentication\Http\Repositories\Contracts\OTPContract;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;
use Modules\Authentication\Http\Repositories\Contracts\StaffContract;
use Modules\Authentication\Http\Repositories\Contracts\SuperadminContract;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserContract::class, UserRepository::class);
        $this->app->bind(StaffContract::class, StaffRepository::class);
        $this->app->bind(SuperadminContract::class, SuperadminRepository::class);
        $this->app->bind(OTPContract::class, OTPRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
