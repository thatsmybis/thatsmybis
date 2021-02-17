<?php

namespace App\Http\Controllers;

use App\{Role, User};
use Auth;
use Illuminate\Http\Request;
use Kodeine\Acl\Models\Eloquent\Permission;

class PermissionsController extends Controller
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
     * Show the permissions page.
     *
     * @return \Illuminate\Http\Response
     */
    public function permissions($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['roles']);

        $permissions = Permission::with('roles')->get();
        return view('guild.permissions', [
            'guild'       => $guild,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Sync the database's permissions with whatever you have set here
     *
     * @return \Illuminate\Http\Response
     */
    public function addPermissions()
    {
        $roles = Role::all();

        $ban = Permission::where('name', 'ban')->first();
        if (!$ban) {
            $ban = Permission::create([
                'name'        => 'ban',
                'slug'        => 'ban',
                'description' => 'Can ban users.'
            ]);
        }

        // $raidLeaderPermissions = Permission::where('name', 'Raid Leader')->first();
        // if (!$raidLeaderPermissions) {
        //     $raidLeaderPermissions = Permission::create([
        //         'name'        => 'Raid Leader',
        //         'slug'        => 'raid_leader',
        //         'description' => 'Can manage their raiders and raid announcements.'
        //     ]);
        // }

        // $contentCreatorPermissions = Permission::where('name', 'Content Creator')->first();
        // if (!$contentCreatorPermissions) {
        //     $contentCreatorPermissions = Permission::create([
        //         'name'        => 'Content Creator',
        //         'slug'        => 'content_creator',
        //         'description' => 'Can create and manage resources.'
        //     ]);
        // }

        // $testingPermissions = Permission::where('name', 'Raider')->first();
        // if (!$testingPermissions) {
        //     $testingPermissions = Permission::create([
        //         'name'        => 'Raider',
        //         'slug'        => 'raider',
        //         'description' => 'This is just here for testing.'
        //     ]);
        // }

        $admin = Role::where('discord_id', env('GUILD_ADMIN_ROLE'))->first();
        $officer = Role::where('discord_id', env('GUILD_OFFICER_ROLE'))->first();
        $guildMaster = Role::where('discord_id', env('GUILD_MASTER_ROLE'))->first();
        $raidLeader = Role::where('discord_id', env('GUILD_RAID_LEADER_ROLE'))->first();
        $classLeader = Role::where('discord_id', env('GUILD_CLASS_LEADER_ROLE'))->first();
        $tester = Role::where('discord_id', 639511059803275294)->first();

        $ban->roles()->sync([$admin->id, $officer->id, $guildMaster->id, $tester->id]);
        // $guildMaster->permissions()->sync($adminPermissions->id);
        // $officer->permissions()->sync($adminPermissions->id);
        // $raidLeader->permissions()->sync($raidLeaderPermissions->id);
        // $classLeader->permissions()->sync($contentCreatorPermissions->id);
        // $testingPermissions->roles()->sync([$tester->id]);

        request()->session()->flash('status', 'Permissions added.');
        return redirect()->route('guild.permissions');
    }
}
