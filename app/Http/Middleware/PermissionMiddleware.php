<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * This Middleware is used to check if the user has the permission to access the route
     * Based On Permission Middleware's Logic
     * From Spatie\Permission\Middlewares\PermissionMiddleware
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user()->fresh();
        $authenticatedUserPermissions = $user->assigned_permissions;
        $routeName = $request->route()->getName();

        if (in_array($routeName, $authenticatedUserPermissions)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Forbidden Access',
        ], 403);
    }
}
