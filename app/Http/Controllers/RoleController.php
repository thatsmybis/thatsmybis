<?php

namespace App\Http\Controllers;

use App\{Guild, Role, User};
use Auth;
use Illuminate\Http\Request;
use RestCord\DiscordClient;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser']);
    }

    /**
     * Show the roles page.
     *
     * @return \Illuminate\Http\Response
     */
    public function roles($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with(['roles'])->firstOrFail();

        // TODO: validate user can view this page

        return view('guild.roles', [
            'guild' => $guild,
        ]);
    }

    /**
     * Sync the database's roles with those on the Discord server
     *
     * @return \Illuminate\Http\Response
     */
    public function syncRoles($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with(['raids', 'roles'])->firstOrFail();

        // TODO: Validate user can do sync these roles

        $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);

        // List of roles that we already have (local)
        $localRoles = $guild->roles;

        // List of roles fetched from Discord (remote)
        $remoteRoles = $discord->guild->getGuildRoles(['guild.id' => $guild->discord_id]);

        $updatedCount = 0;
        $addedCount = 0;
        $removedCount = 0;

        // Iterate over the roles in remote
        foreach ($remoteRoles as $remoteRole) {
            if (!$localRoles->contains('discord_id', $remoteRole->id)) {
            // Role not found in local: Add role to local
                Role::create([
                    'name'        => $remoteRole->name,
                    'guild_id'    => $guild->id,
                    'discord_id'  => (int)$remoteRole->id,
                    'permissions' => $remoteRole->permissions,
                    'position'    => $remoteRole->position,
                    'color'       => $remoteRole->color,
                    'slug'        => slug($remoteRole->name),
                    'description' => '',
                ]);
                $addedCount++;
            } else {
            // Role found in local: Update role in local to match remote
                $localRole = $localRoles->where('discord_id', $remoteRole->id)->first();

                $localRole->color = $remoteRole->color ? $remoteRole->color : null;
                $localRole->name = $remoteRole->name;
                $localRole->slug = slug($remoteRole->name);
                $localRole->position = $remoteRole->position;

                $localRole->save();
                $updatedCount++;
            }
        }

        // Iterate over the roles in local
        foreach ($localRoles as $localRole) {
            $found = false;
            foreach ($remoteRoles as $remoteRole) {
                if ($remoteRole->id == $localRole->discord_id) {
                    $found = true;
                    break;
                }
            }

            // Role in local doesn't exist in remote: delete local role
            if (!$found) {
                $localRole->delete();
                $removedCount++;
            }
        }

        request()->session()->flash('status', $updatedCount . ' roles synced. ' . $addedCount . ' roles added. ' . $removedCount . ' roles removed.');
        return redirect()->route('guild.roles', ['guildSlug' => $guild->slug]);
    }
}
