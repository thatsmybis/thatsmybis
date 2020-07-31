<?php

namespace App\Http\Middleware;

use Auth, Closure;
use App\Role;
use App\Notification;
use RestCord\DiscordClient;

class SeeUser
{
    /**
     * Handle an incoming request.
     *
     * Stores the user object in the request for convenient access later on.
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
            }

            // Store the user for later access.
            $request->attributes->add(['currentUser'  => $user]);
            $request->attributes->add(['isStreamerMode' => $user->is_streamer_mode]);
        }

        return $next($request);
    }
}
