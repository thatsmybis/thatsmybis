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

            try {
                $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);
                $discordMember = $discord->guild->getGuildMember(['guild.id' => (int)env('GUILD_ID'), 'user.id' => (int)$user->discord_id]);
                $roles = Role::whereIn('discord_id', $discordMember->roles)->get();
                $user->syncRoles($roles);
            } catch (\GuzzleHttp\Command\Exception\CommandClientException $e) {
                abort(404, "Doesn't look like you're in the guild Discord server.");
            }
        }
        return $next($request);
    }
}
