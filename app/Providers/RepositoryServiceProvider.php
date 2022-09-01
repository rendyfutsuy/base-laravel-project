<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\RoleRepository;
use App\Http\Repositories\UserRepository;
use App\Http\Repositories\StaffRepository;
use App\Http\Repositories\ExampleRepository;
use App\Http\Repositories\PermissionRepository;
use App\Http\Repositories\SuperadminRepository;
use App\Http\Repositories\Contracts\RoleContract;
use App\Http\Repositories\Contracts\UserContract;
use App\Http\Repositories\Contracts\StaffContract;
use App\Http\Repositories\Contracts\ExampleContract;
use App\Http\Repositories\Contracts\PermissionContract;
use App\Http\Repositories\Contracts\SuperadminContract;
use App\Http\Repositories\Contracts\BaseRepositoryContract;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(BaseRepositoryContract::class, BaseRepository::class);
        $this->app->bind(PermissionContract::class, PermissionRepository::class);
        $this->app->bind(RoleContract::class, RoleRepository::class);
        $this->app->bind(UserContract::class, UserRepository::class);
        $this->app->bind(StaffContract::class, StaffRepository::class);
        $this->app->bind(SuperadminContract::class, SuperadminRepository::class);
        $this->app->bind(ExampleContract::class, ExampleRepository::class);
    }
}
