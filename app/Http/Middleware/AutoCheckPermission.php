<?php

namespace App\Http\Middleware;

use App\Permission;
use Closure;

class AutoCheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route_name = $request->route()->getName();
        $permission = Permission::whereRaw("FIND_IN_SET ('$route_name',route_name)")->first();
        if ($permission) {
            if ($request->user()->can($permission->name)) {
                return $next($request);
            }
            abort(403);
        }
        abort(403);
    }
}