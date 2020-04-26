<?php

namespace App\Http\Middleware;

use Closure;

class UserActiveStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$allow)
    {
        if (in_array(User_getActiveStatus(User_getActiveStatusByCode($request->user()->code)), $allow)) {
            return $next($request);
        }
        return response()->json(errorResponse('Account has been ' . User_getActiveStatus($request->user()->active) . ' due to bad behavior.'), 202);
    }
}
