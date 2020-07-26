<?php

namespace App\Http\Middleware;

use Auth, Closure;
use App\{Guild, Member};
use Exception;
use RestCord\DiscordClient;

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
                    return $query->where('members.user_id', Auth::id())->with('roles');
                },
            ])->first();

            if (!$guild) {
                abort(404, 'Guild not found.');
            }

            $user = request()->get('currentUser');
            if (!$user) {
                request()->session()->flash('status', 'You need to be signed in to do that.');
                return redirect()->route('login');
            }

            $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);

            $discordMember = null;

            // Check if current user is on that guild's Discord
            try {
                $discordMember = $discord->guild->getGuildMember(['guild.id' => (int)$guild->discord_id, 'user.id' => (int)$user->discord_id]);
            } catch (Exception $e) {
                if ($user->id != $guild->user_id) { // Guild owner is excempt
                    request()->session()->flash('status', 'You don\'t appear to be a member of that guild\'s  Discord.');
                    return redirect()->route('home');
                }
            }

            // Guild owner doesn't have to go through this process
            // This ensures they never lock themselves out due to messing with roles
            if ($user->id != $guild->user_id) {
                // Check that the Discord user has one of the role(s) required to access this guild
                $matchingRoles = array_intersect($guild->getMemberRoleIds(), $discordMember->roles);

                if (count($matchingRoles) <= 0) {
                    request()->session()->flash('status', 'Insufficient Discord role to access that guild.');
                    return redirect()->route('home');
                }

                // They're on the Discord and they have an appropriate role if they get this far
            }

            // Fetch their existing member object
            $currentMember = $guild->members->where('user_id', Auth::id())->first();

            if (!$currentMember) {
                // Don't have a member object? Let's create one...
                $currentMember = Member::create($user, $discordMember, $guild);
            } else if ($discordMember) {
                // Does the member have any new/missing roles since we last checked?
                $storedRoles = $currentMember->roles->keyBy('discord_id')->keys()->toArray();

                // Compare Discord's roles vs. our DB's roles
                $diffRoles = array_merge(array_diff($storedRoles, $discordMember->roles), array_diff($discordMember->roles, $storedRoles));

                // The roles we have vs. what Discord has differ.
                if ($diffRoles) {
                    // Sync their roles with the db
                    $currentMember->syncRoles($guild, $discordMember);
                }
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
