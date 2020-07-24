<?php

namespace App\Http\Middleware;

use Auth, Closure;
use App\{Guild};

class CheckGuildPermissions
{
    /**
     * Handle an incoming request.
     *
     * Stores guild and currentMember objects in the request for convenient access later on.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->route('guildSlug') !== null) {
            $guild = Guild::where('slug', $request->route('guildSlug'))->with([
                'members' => function ($query) {
                    return $query->where('members.user_id', Auth::id());
                },
            ])->first();

            if (!$guild) {
                abort(404, 'Guild not found.');
            }

            $currentMember = $guild->members->where('user_id', Auth::id())->first();

            if (!$currentMember) {
                abort(403, 'Not a member of that guild.');
            }

            // Store the guild and current member for later access.
            $request->attributes->add([
                'currentMember' => $currentMember,
                'guild'         => $guild,
            ]);
        }

        return $next($request);
    }
}
