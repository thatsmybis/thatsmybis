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

            $cacheKey = 'guild:' . $request->route('guildId');

            if (request()->get('bustCache')) {
                Cache::forget($cacheKey);
            }

            $guild = Cache::remember($cacheKey, env('CACHE_GUILD_SECONDS', 5), function () use ($request) {
                return Guild::
                    where('id', $request->route('guildId'))
                    ->with([
                        'allRaidGroups',
                        'allRaidGroups.role',
                        'raidGroups' => function ($query) { return $query->whereNull('disabled_at'); }
                    ])
                    ->first();
            });

            if (!$guild) {
                abort(404, __('Guild not found.'));
            }

            if ($guild->slug !== $request->route('guildSlug')) {
                $request->route()->setParameter('guildSlug', $guild->slug);
            }

            $user    = request()->get('currentUser');
            $isAdmin = request()->get('isAdmin');

            if (!$user) {
                // Exception for this page; redirect them to the public one
                if (request()->route()->getName() === 'guild.loot.wishlist') {
                    return redirect()->route('loot.wishlist', ['expansionName' => getExpansionAbbr($guild->expansion_id, true), 'class' => request()->route('class')]);
                }

                request()->session()->flash('status', __('You need to be signed in to do that.'));
                return redirect()->route('login');
            }

            $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);

            $discordMember = null;

            $cacheKey = 'user:' . $user->id . ':guild:' . $guild->id . ':discordMember';

            if (request()->get('bustCache')) {
                Cache::forget($cacheKey);
            }

            // Check if current user is on that guild's Discord
            // Cache the results
            $discordMember = Cache::remember($cacheKey, env('DISCORD_ROLE_CACHE_SECONDS', 30),
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
                    // Exception for this page; redirect them to the public one
                    if (request()->route()->getName() === 'guild.loot.wishlist') {
                        return redirect()->route('loot.wishlist', ['expansionName' => getExpansionAbbr($guild->expansion_id, true), 'class' => request()->route('class')]);
                    }

                    request()->session()->flash('status', __("That Discord server is either missing the :appName bot or we're unable to find you on it.", ['appName' => env('APP_NAME')]));
                    return redirect()->route('home');
                }

                // Guild owner doesn't have to go through this process
                // This ensures they never lock themselves out due to messing with roles
                if ($user->id != $guild->user_id && !$isAdmin) {
                    if ($guild->getMemberRoleIds()) {
                        // Check that the Discord user has one of the role(s) required to access this guild
                        $matchingRoles = array_intersect(array_merge($guild->getMemberRoleIds(), [$guild->gm_role_id, $guild->officer_role_id, $guild->raid_leader_role_id]), $discordMember->roles);

                        if (count($matchingRoles) <= 0) {
                            request()->session()->flash('status', __('Insufficient Discord role to access that guild.'));
                            return redirect()->route('home');
                        }
                    }

                    // They're on the Discord and they have an appropriate role if they get this far
                }

                // Check if the guild is disabled
                if ($guild->disabled_at && $user->id != $guild->user_id && !$isAdmin) {
                    $message = '';
                    $message .= __(':guildName disabled by guild master.', ['guildName' => $guild->name]);
                    if ($guild->message) {
                        $message .= '<br><strong>' . __('Message of the Day:') . '</strong><br>' . nl2br($guild->message);
                    }

                    request()->session()->flash('status-danger',  $message);
                    return redirect()->route('home');
                }
            }

            // Fetch their existing member object
            $currentMember = Member::where(['guild_id' => $guild->id, 'user_id' => Auth::id()])->with(['characters', 'roles'])->first();

            if ($currentMember && ($currentMember->banned_at || $currentMember->inactive_at) && !$isAdmin) {
                request()->session()->flash('status-danger',  __('Your membership has been disabled. To reverse this, an officer would need to access your member page and re-enable it.'));
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
                        abort(403, __("You must have at least one member object tied to your account to access someone else's guild as an admin. The code demands there must always be a member object!"));
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
                'currentMember'     => $currentMember,
                'isGuildAdmin'      => ($guild->user_id == $currentMember->user_id || $isAdmin),
                'guild'             => $guild,
                'raidGroupIdFilter' => $currentMember ? $currentMember->raid_group_id_filter : null,
            ]);
        }

        return $next($request);
    }
}
