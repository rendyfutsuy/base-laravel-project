<?php

namespace Modules\UserManagement\Providers;

use Illuminate\Support\ServiceProvider;

class UserManagementServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__.'/../Http/helpers.php';
        $this->registerConfig();

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/user-management.php' => config_path('user-management.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../Config/user-management.php', 'user-management');

        $this->publishes([
            __DIR__.'/../Config/auth.php' => config_path('auth.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../Config/auth.php', 'user-management.auth');

        $this->publishes([
            __DIR__.'/../Config/seeder.php' => config_path('seeder.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../Config/seeder.php', 'user-management.seeder');

        $this->publishes([
            __DIR__.'/../Config/routing.php' => config_path('routing.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../Config/routing.php', 'user-management.routing');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
