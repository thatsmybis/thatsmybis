<?php

namespace App\Http\Middleware;

use Auth, Closure;
use App\Notification;

class SeeUser
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
        $user = $request->user();
        if ($user) {
            // You shall not pass!
            if ($user->banned_at) {
                Auth::guard()->logout();
                $request->session()->invalidate();
                abort(403, 'You have been banned.');
            // Who?
            }
        }
        return $next($request);
    }
}
