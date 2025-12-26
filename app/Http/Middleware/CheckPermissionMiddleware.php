<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;

class CheckPermissionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $controller = class_basename($request->route()->getControllerClass());
        $method = $request->route()->getActionMethod();

        $permission = "$controller@$method";

        if (!Auth::user()->hasPermissionTo($permission)) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
