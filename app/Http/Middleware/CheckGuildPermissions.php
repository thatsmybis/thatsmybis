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

            $user    = request()->get('currentUser');
            $isAdmin = request()->get('isAdmin');

            if (!$user) {
                request()->session()->flash('status', 'You need to be signed in to do that.');
                return redirect()->route('login');
            }

            $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);

            $discordMember = null;

            // Check if current user is on that guild's Discord
            // Cache to results
            $discordMember = Cache::remember('user:' . $user->id . ':guild:' . $guild->id . ':discordMember', env('DISCORD_ROLE_CACHE_SECONDS'),
                function () use ($user, $guild) {
                    try {
                        $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);
                        return $discord->guild->getGuildMember([
                            'guild.id' => (int)$guild->discord_id,
                            'user.id' => (int)$user->discord_id
                        ]);
                    } catch (Exception $e) {
                        // Fail silently
                        return null;
                    }
                }
            );

            if ($discordMember && $discordMember->user->username . '#' . $discordMember->user->discriminator != $user->discord_username) {
                $user->update([
                    'discord_username' => $discordMember->user->username . '#' . $discordMember->user->discriminator,
                ]);
            }

            // Don't do these checks if the member is trying to gquit...
            if (!request()->routeIs('member.showGquit') && !request()->routeIs('member.submitGquit')) {
                if (!$discordMember && $user->id != $guild->user_id && !$isAdmin) { // Guild owner gets a pass
                    request()->session()->flash('status', 'That Discord server is either missing the ' . env('APP_NAME') . ' bot or we\'re unable to find you on it.');
                    return redirect()->route('home');
                }

                // Guild owner doesn't have to go through this process
                // This ensures they never lock themselves out due to messing with roles
                if ($user->id != $guild->user_id && !$isAdmin) {
                    if ($guild->getMemberRoleIds()) {
                        // Check that the Discord user has one of the role(s) required to access this guild
                        $matchingRoles = array_intersect(array_merge($guild->getMemberRoleIds(), [$guild->gm_role_id, $guild->officer_role_id, $guild->raid_leader_role_id]), $discordMember->roles);

                        if (count($matchingRoles) <= 0) {
                            request()->session()->flash('status', 'Insufficient Discord role to access that guild.');
                            return redirect()->route('home');
                        }
                    }

                    // They're on the Discord and they have an appropriate role if they get this far
                }

                // Check if the guild is disabled
                if ($guild->disabled_at && $user->id != $guild->user_id && !$isAdmin) {
                    $message = '';
                    $message .= $guild->name . ' disabled by guild master.';
                    if ($guild->message) {
                        $message .= '<br><strong>Message of the Day:</strong><br>' . nl2br($guild->message);
                    }

                    request()->session()->flash('status-danger',  $message);
                    return redirect()->route('home');
                }
            }

            // Fetch their existing member object
            $currentMember = Member::where(['guild_id' => $guild->id, 'user_id' => Auth::id()])->with('roles')->first();

            if ($currentMember && ($currentMember->banned_at || $currentMember->inactive_at) && !$isAdmin) {
                request()->session()->flash('status-danger',  'Your membership has been disabled. To reverse this, an officer would need to access your member page and re-enable it.');
                return redirect()->route('home');
            }

            if (!$currentMember) {

                if ($discordMember) {
                    // Don't have a member object? Let's create one...
                    $currentMember = Member::create($user, $discordMember, $guild);

                    AuditLog::create([
                        'description'     => $currentMember->username . ' joined',
                        'member_id'       => $currentMember->id,
                        'guild_id'        => $guild->id,
                    ]);
                } else {
                    $currentMember = Member::where('user_id', $user->id)->first();
                    if (!$currentMember) {
                        abort(403, "You must have at least one member object tied to your account to access someone else's guild as an admin. The code demands there must always be a member object!");
                    }
                    $request->attributes->add(['isNotYourGuild' => true]);
                }
            } else if ($discordMember) {
                // TODO: Remove $doHotfix after 2021. This was added to resolve a bug that was live for a few weeks when
                // TBC was released on thatsmybis. The bug affected records in the database.
                $doHotfix = ($currentMember->roles->count() > 0 && $currentMember->roles->last()->updated_at < '2021-03-02 05:45:00');

                if ($doHotfix) {
                    $currentMember->roles()->detach();
                    $currentMember->load('roles');
                }

                // Does the member have any new/missing roles since we last checked?
                $storedRoles = $currentMember->roles->keyBy('discord_id')->keys()->toArray();

                // Compare Discord's roles vs. our DB's roles
                $diffRoles = array_merge(array_diff($storedRoles, $discordMember->roles), array_diff($discordMember->roles, $storedRoles));

                // The roles we have vs. what Discord has differ.
                if ($diffRoles || $doHotfix) {
                    // Sync their roles with the db
                    $currentMember->syncRoles($guild, $discordMember);
                }
            }

            // Store the guild and current member for later access.
            $request->attributes->add([
                'currentMember' => $currentMember,
                'isGuildAdmin'  => ($guild->user_id == $currentMember->user_id || $isAdmin),
                'guild'         => $guild,
            ]);
        }

        return $next($request);
    }
}
