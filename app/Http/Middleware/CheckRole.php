<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param array ...$roles
     * @return void
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (in_array(User_getStatus(User_getStatusByCode($request->user()->code)), $roles)) {
            return $next($request);
        }
        return response()->json(errorResponse('Unauthorized.'), 401);
    }
}
