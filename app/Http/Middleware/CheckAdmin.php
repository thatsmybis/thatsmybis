<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdmin
{
    /**
     * Check that the user is an admin before proceeding.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!request()->get('isAdmin')) {
            request()->session()->flash('status', 'Admins only. И0 plɘbƧ All0wɘD! :(');
            return redirect()->route('home');
        }

        return $next($request);
    }
}
