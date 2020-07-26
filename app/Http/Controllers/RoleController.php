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
    public function roles($guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        // TODO: validate user can view this page

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
    public function syncRoles($guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['roles']);

        // TODO: Validate user can do sync these roles

        $result = Role::syncWithDiscord($guild);

        request()->session()->flash('status', $result['updatedCount'] . ' roles synced. ' . $result['addedCount'] . ' roles added. ' . $result['removedCount'] . ' roles removed.');
        return redirect()->route('guild.roles', ['guildSlug' => $guild->slug]);
    }
}
