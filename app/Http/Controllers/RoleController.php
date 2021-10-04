<?php

namespace App\Http\Controllers;

use App\{Guild, Role, User};
use Auth;
use Illuminate\Http\Request;

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
    public function roles($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('view.discord-roles')) {
            request()->session()->flash('status', __("You don't have permissions to view that page."));
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load('roles');

        return view('guild.roles', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
        ]);
    }

    /**
     * Sync the database's roles with those on the Discord server
     *
     * @return \Illuminate\Http\Response
     */
    public function syncRoles($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('sync.discord-roles')) {
            request()->session()->flash('status', __("You don't have permissions to sync roles."));
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load(['roles']);

        $result = Role::syncWithDiscord($guild);

        request()->session()->flash('status',
            __(":updatedCount roles synced. :addedCount roles added. :removedCount roles removed.", [
                'updatedCount' => $result['updatedCount'],
                'addedCount'   => $result['addedCount'],
                'removedCount' => $result['removedCount'],
            ])
        );

        return redirect()->route('guild.roles', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
    }
}
