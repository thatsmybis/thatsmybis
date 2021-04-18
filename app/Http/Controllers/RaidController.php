<?php

namespace App\Http\Controllers;

use App\{AuditLog, Guild, Instance, Raid, RaidGroup};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Kodeine\Acl\Models\Eloquent\Permission;

class RaidController extends Controller
{
    const MAX_CHARACTERS = 160;
    const MAX_INSTANCES  = 4;
    const MAX_RAIDS      = 4;
    const RESULTS_PER_PAGE = 20;

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
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        // TODO: Is this permission check ok? Different permission?
        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to view that page.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
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
            'currentMember'   => $currentMember,
            'guild'           => $guild,
            'instances'       => $instances,
            'maxCharacters'   => self::MAX_CHARACTERS,
            'maxInstances'    => self::MAX_INSTANCES,
            'maxRaids'        => self::MAX_RAIDS,
            'raid'            => $raid,
            'showOfficerNote' => $showOfficerNote,
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
            request()->session()->flash('status', 'You don\'t have permissions to create Raids.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules = [
            'date'            => 'required|date_format:Y-m-d H:i:s',
            'name'            => 'required|string|max:75',
            'public_note'     => 'nullable|string|max:250',
            'officer_note'    => 'nullable|string|max:250',
            'logs'            => 'nullable|string|max:250',
            'instance_id.*'   => [
                'nullable',
                'integer',
                Rule::exists('instances', 'id')->where('instances.expansion_id', $guild->expansion_id),
            ],
            'raid_group_id.*' => [
                'nullable',
                'integer',
                Rule::exists('raid_groups', 'id')->where('raid_groups.guild_id', $guild->id),
            ],
            'characters.*.character_id' => [
                'nullable',
                'integer',
                Rule::exists('characters', 'id')->where('characters.guild_id', $guild->id),
            ],
            'characters.*.is_exempt'    => 'nullable|boolean',
            'characters.*.remark'       => ['nullable', 'string', Rule::in(Raid::remarks())],
            'characters.*.credit'       => 'required|numeric|between:0,1',
            'characters.*.note'         => 'nullable|string|max:250',
            'characters.*.officer_note' => 'nullable|string|max:250',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $createValues = [];

        $createValues['date']         = request()->input('date');
        $createValues['name']         = request()->input('name');
        $createValues['public_note']  = request()->input('public_note');
        $createValues['officer_note'] = request()->input('officer_note');
        $createValues['logs']         = request()->input('logs');

        $createValues['slug']         = slug(request()->input('name'));
        $createValues['guild_id']     = $guild->id;
        $createValues['member_id']    = $currentMember->id;
        $createValues['is_cancelled'] = 0;

        $raid = Raid::create($createValues);

        $characterCount = 0;
        $instanceCount = 0;
        $raidGroupCount = 0;
        $newRows = [];

        // Add characters
        foreach (request()->input('characters') as $character) {
            if ($character['character_id']) {
                $newRows[] = [
                    'raid_id'      => $raid,
                    'character_id' => $character['character_id'],
                    'is_exempt'    => isset($character['is_exempt']) && $character['is_exempt'] == 1 ? 1 : 0,
                    'remark'       => ($character['remark'] ? $character['remark'] : null),
                    'credit'       => ($character['credit'] ? floatval($character['credit']) : 0.0),
                    'note'         => ($character['note'] ? $character['note'] : null),
                    'officer_note' => ($character['officer_note'] ? $character['officer_note'] : null),
                ];
                $characterCount++;
            }
        }
        DB::table('raid_characters')->insert($newRows);
        $newRows = [];

        // Add instances
        foreach (request()->input('instance_id') as $instanceId) {
            if ($instanceId) {
                $newRows[] = [
                    'raid_id'     => $raid,
                    'instance_id' => $instanceId,
                ];
                $instanceCount++;
            }
        }
        DB::table('raid_instances')->insert($newRows);
        $newRows = [];

        // Add raid groups
        foreach (request()->input('raid_group_id') as $raidGroupId) {
            if ($raidGroupId) {
                $newRows[] = [
                    'raid_id'       => $raid,
                    'raid_group_id' => $raidGroupId,
                ];
                $raidGroupCount++;
            }
        }
        DB::table('raid_raid_groups')->insert($newRows);
        $newRows = [];

        AuditLog::create([
            'description'   => $currentMember->username . " created raid \"{$raid->name}\" with {$characterCount} character(s), {$instanceCount} dungeon(s), and {$raidGroupCount} raid group(s)",
            'member_id'     => $currentMember->id,
            'guild_id'      => $guild->id,
            'raid_group_id' => ($raidGroupCount ? request()->input('raid_group_id')[0] : null),
            'raid_id'       => $raid->id,
        ]);

        request()->session()->flash('status', "Successfully created Raid {$raid->name}.");
        return redirect()->route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raid->id, 'slug' => $raid->slug]);
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
