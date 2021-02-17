<?php

namespace App\Http\Middleware;

use Auth, Closure;
use App\{AuditLog, Guild, Member};
use Exception;
use RestCord\DiscordClient;
use Illuminate\Support\Facades\Cache;

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
        if ($request->route('guildId') !== null) {
            $guild = Guild::where('id', $request->route('guildId'))->first();

            if (!$guild) {
                abort(404, 'Guild not found.');
            }

            if ($guild->slug !== $request->route('guildSlug')) {
                $request->route()->setParameter('guildSlug', $guild->slug);
            }

            $user = request()->get('currentUser');
            if (!$user) {
                request()->session()->flash('status', 'You need to be signed in to do that.');
                return redirect()->route('login');
            }

            $discordMember = null;

            // Check if current user is on that guild's Discord
            $discordMember = Cache::remember(
                'user:' . $user->id . ':guild:' . $guild->id . ':discordMember',
                env('DISCORD_ROLE_CACHE_SECONDS'),
                function ()  use ($user, $guild) {
                    try {
                        $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);
                        return $discord->guild->getGuildMember([
                            'guild.id' => (int)$guild->discord_id,
                            'user.id' => (int)$user->discord_id
                        ]);
                    } catch (Exception $e) {
                        // Yeah, I know...
                        return null;
                    }
                });

            if (!$discordMember && $user->id != $guild->user_id) { // Guild owner gets a pass
                request()->session()->flash('status', 'That Discord server is either missing the ' . env('APP_NAME') . ' bot or we\'re unable to find you on it.');
                return redirect()->route('home');
            }

            // Guild owner doesn't have to go through this process
            // This ensures they never lock themselves out due to messing with roles
            if ($user->id != $guild->user_id) {
                if ($guild->getMemberRoleIds()) {
                    // Check that the Discord user has one of the role(s) required to access this guild
                    $matchingRoles = array_intersect($guild->getMemberRoleIds(), $discordMember->roles);

                    if (count($matchingRoles) <= 0) {
                        request()->session()->flash('status', 'Insufficient Discord role to access that guild.');
                        return redirect()->route('home');
                    }
                }

                // They're on the Discord and they have an appropriate role if they get this far
            }

            // Check if the guild is disabled
            if ($guild->disabled_at && $user->id != $guild->user_id) {
                $message = '';
                $message .= $guild->name . ' disabled by guild master.';
                if ($guild->message) {
                    $message .= '<br><strong>Message of the Day:</strong><br>' . nl2br($guild->message);
                }

                request()->session()->flash('status-danger',  $message);
                return redirect()->route('home');
            }

            // Fetch their existing member object
            $currentMember = Member::where(['guild_id' => $guild->id, 'user_id' => Auth::id()])->first();

            if ($currentMember && ($currentMember->banned_at || $currentMember->inactive_at)) {
                request()->session()->flash('status-danger',  'Your membership has been disabled. To reverse this, an officer would need to access your member page and re-enable it.');
                return redirect()->route('home');
            }

            if (!$currentMember) {
                // Don't have a member object? Let's create one...
                $currentMember = Member::create($user, $discordMember, $guild);

                AuditLog::create([
                    'description'     => $currentMember->username . ' joined',
                    'member_id'       => $currentMember->id,
                    'guild_id'        => $guild->id,
                ]);
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
                'isSuperAdmin'  => ($guild->user_id == $currentMember->user_id),
            ]);
        }

        return $next($request);
    }
}
