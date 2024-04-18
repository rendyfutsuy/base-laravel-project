<?php

namespace Modules\Authentication\Providers;

use Illuminate\Support\ServiceProvider;

class AuthenticationServiceProvider extends ServiceProvider
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
            __DIR__.'/../Config/ram.php' => config_path('ram.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../Config/ram.php', 'ram');

        $this->publishes([
            __DIR__.'/../Config/auth.php' => config_path('auth.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../Config/auth.php', 'ram.auth');

        $this->publishes([
            __DIR__.'/../Config/seeder.php' => config_path('seeder.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../Config/seeder.php', 'ram.seeder');

        $this->publishes([
            __DIR__.'/../Config/routing.php' => config_path('routing.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../Config/routing.php', 'ram.routing');
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
