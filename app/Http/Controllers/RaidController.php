<?php

namespace App\Http\Controllers;

use App\{AuditLog, Guild, Instance, Raid, RaidGroup};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Kodeine\Acl\Models\Eloquent\Permission;

class RaidController extends Controller
{
    const RESULTS_PER_PAGE = 20;
    const MAX_INSTANCES = 4;
    const MAX_RAIDS     = 4;

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
     * Show a raid for editing, or creating if no ID is provided
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($guildId, $guildSlug, $id = null) {
        // TODO: Copied from raidgroups
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        // TODO: Is this permission check ok? Dufferenet permission?
        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to view that page.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $raid = null;

        if ($id) {
            $raid = Raid::where([
                ['guild_id', $guild->id],
                ['id', $id],
            ])->first();
        }

        $guild->load([
            'characters',
            'raidGroups',
            'raidGroups.role']);

        $instances = Instance::where('expansion_id', $guild->expansion_id)->get();

        return view('guild.raids.edit', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'instances'     => $instances,
            'maxInstances'  => self::MAX_INSTANCES,
            'maxRaids'      => self::MAX_RAIDS,
            'raid'          => $raid,
        ]);
    }

    /**
     * Create a raid
     * @return
     */
    public function create($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('create.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to create Raid Groups.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load(['allRaidGroups', 'roles']);

        $validationRules = [
            'name'    => 'string|max:255',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        if ($guild->raidGroups->contains('name', request()->input('name'))) {
            abort(403, 'Name already exists.');
        }

        $role = null;

        if (request()->input('role_id')) {
            $role = $guild->roles->where('id', request()->input('role_id'));
            if (!$role) {
                abort(404, 'Role not found.');
            }
        }

        $createValues = [];

        $createValues['name']     = request()->input('name');
        $createValues['slug']     = slug(request()->input('name'));
        $createValues['role_id']  = request()->input('role_id');
        $createValues['guild_id'] = $guild->id;

        $raidGroup = RaidGroup::create($createValues);

        AuditLog::create([
            'description'   => $currentMember->username . ' created a Raid Group',
            'member_id'     => $currentMember->id,
            'guild_id'      => $guild->id,
            'raid_group_id' => $raidGroup->id,
        ]);

        request()->session()->flash('status', 'Successfully created Raid Group.');
        return redirect()->route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
    }

    /**
     * Show the raids list page.
     *
     * @return \Illuminate\Http\Response
     */
    public function list($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['characters', 'members', 'raidGroups', 'raidGroups.role']);

        $raids = Raid::select([
                'raids.*',
                DB::raw('COUNT(DISTINCT `raid_characters`.`character_id`) AS `raider_count`'),
                DB::raw('COUNT(DISTINCT `raid_items`.`item_id`) AS `item_count`'),
            ])
            ->leftJoin('raid_characters', 'raid_characters.raid_id', '=', 'raids.id')
            ->where('raids.guild_id', $guild->id)
            ->orderBy('raids.date', 'desc')
            ->with(['instances', 'member', 'raidGroups', 'raidGroups.role'])
            ->paginate(self::RESULTS_PER_PAGE);

        return view('guild.raids.list', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raids'         => $raids,
        ]);
    }

    /**
     * Show a raid
     *
     * @return \Illuminate\Http\Response
     */
    public function show($guildId, $guildSlug, $id = null) {
        // TODO: Copied from raidgroups EDIT function
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to view that page.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load([
            'allRaidGroups' => function ($query) use ($id) {
                return $query->where('id', $id);
            },
            'allRaidGroups.role']);

        $raidGroup = null;

        if ($id) {
            $raidGroup = $guild->allRaidGroups->where('id', $id)->first();

            if (!$raidGroup) {
                abort(404, 'Raid Group not found.');
            }
        }

        return view('guild.raidGroups.edit', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raidGroup'     => $raidGroup,
        ]);
    }

    /**
     * Update a raid
     * @return
     */
    public function update($guildId, $guildSlug) {
        // TODO: Copied from raidgroups
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit Raid Groups.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules =  [
            'id'      => 'required|integer|exists:raid_groups,id',
            'name'    => 'string|max:255',
            'role_id' => 'nullable|integer|exists:roles,id',
        ];

        $this->validate(request(), $validationRules);

        $id = request()->input('id');

        $guild->load([
            'allRaidGroups' => function ($query) use ($id) {
                return $query->where('id', $id);
            },
        ]);

        $raidGroup = $guild->allRaidGroups->where('id', $id)->first();
        if (!$raidGroup) {
            abort(404, 'Raid Group not found.');
        }

        $role = null;
        if (request()->input('role_id')) {
            $role = $guild->roles->where('id', request()->input('role_id'));
            if (!$role) {
                abort(404, 'Role not found.');
            }
        }

        $updateValues = [];

        $updateValues['name']    = request()->input('name');
        $updateValues['slug']    = slug(request()->input('name'));
        $updateValues['role_id'] = request()->input('role_id');

        $auditMessage = '';

        if ($updateValues['name'] != $raidGroup->name) {
            $auditMessage .= ' (renamed to ' . $updateValues['name'] . ')';
        }

        $raidGroup->update($updateValues);

        AuditLog::create([
            'description'   => $currentMember->username . ' updated a Raid Group' . $auditMessage,
            'member_id'     => $currentMember->id,
            'guild_id'      => $guild->id,
            'raid_group_id' => $raidGroup->id,
        ]);

        request()->session()->flash('status', 'Successfully updated ' . $raidGroup->name . '.');
        return redirect()->route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
    }
}
