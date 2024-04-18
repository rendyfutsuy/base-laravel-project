<?php

namespace Modules\Location\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Providers\Traits\RoutingRegistration;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    use RoutingRegistration;

    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Modules\\Location\\Http\\Controllers';

    /**
     * @return array<RouteContract>
     */
    protected function paths()
    {
        return config('location.routing.paths');
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //register new route middleware
        $this->customMiddleware();

        $this->configureRateLimiting();

        $this->routes(function () {
            $this->registeredRoutes();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function customMiddleware()
    {
        if (empty(config('location.routing.middleware'))) {
            return;
        }

        foreach (config('location.routing.middleware') as $alias => $class) {
            app('router')->aliasMiddleware($alias, $class);
        }
    }
}
