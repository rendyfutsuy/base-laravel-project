<?php

namespace App\Providers\Traits;

use Illuminate\Support\Facades\Route;

trait RoutingRegistration
{
    /**
     * @return array<RouteContract>
     */
    protected function paths()
    {
        return config('routing.paths');
    }

    protected function registeredRoutes(): void
    {
        $routes = $this->paths();
        foreach ($routes as $route) {
            Route::prefix($route['prefix'])
                ->middleware($route['middleware'])
                ->namespace($this->namespace)
                ->group($route['path']);
        }
    }
}
