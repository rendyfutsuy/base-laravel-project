<?php

namespace Modules\Hierarchy\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Hierarchy\Http\Services\Repositories\RoleRepository;
use Modules\Hierarchy\Http\Services\Repositories\PermissionRepository;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\RoleContract;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\PermissionContract;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            PermissionContract::class,
            PermissionRepository::class
        );

        $this->app->bind(
            RoleContract::class,
            RoleRepository::class
        );
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
