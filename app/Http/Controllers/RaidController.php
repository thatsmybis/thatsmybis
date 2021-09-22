<?php

namespace App\Http\Controllers;

use App\{AuditLog, CharacterItem, Guild, Instance, Log, Raid, RaidCharacter, RaidGroup, RaidInstance, RaidRaidGroup};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Kodeine\Acl\Models\Eloquent\Permission;

class RaidController extends Controller
{
    const MAX_CHARACTERS = 160;
    const MAX_INSTANCES  = 4;
    const MAX_RAID_GROUPS = 4;
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
     * Copy a raid
     *
     * @return \Illuminate\Http\Response
     */
    public function copy($guildId, $guildSlug, $id) {
        return $this->showEdit($guildId, $guildSlug, $id, true);
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

        $validationRules = $this->getValidationRules($guild);

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $createValues = [];

        $createValues['date']         = request()->input('date');
        $createValues['name']         = request()->input('name');
        $createValues['public_note']  = request()->input('public_note');
        $createValues['officer_note'] = request()->input('officer_note');

        $createValues['slug']         = slug(request()->input('name'));
        $createValues['guild_id']     = $guild->id;
        $createValues['member_id']    = $currentMember->id;
        $createValues['cancelled_at'] = null;
        $createValues['archived_at'] = null;

        $raid = Raid::create($createValues);

        $characterCount = 0;
        $instanceCount = 0;
        $raidGroupCount = 0;
        $newRows = [];

        // Add characters
        $alreadyAdded = [];
        foreach (request()->input('characters') as $character) {
            if ($character['character_id'] && !isset($alreadyAdded[$character['character_id']])) {
                $alreadyAdded[$character['character_id']] = true;
                $newRows[] = [
                    'raid_id'      => $raid->id,
                    'character_id' => $character['character_id'],
                    'is_exempt'    => isset($character['is_exempt']) && $character['is_exempt'] == 1 ? 1 : 0,
                    'remark_id'    => ($character['remark_id'] ? $character['remark_id'] : null),
                    'credit'       => ($character['credit'] ? floatval($character['credit']) : 0.0),
                    'public_note'  => ($character['public_note'] ? $character['public_note'] : null),
                    'officer_note' => ($character['officer_note'] ? $character['officer_note'] : null),
                ];
                $characterCount++;
            }
        }
        RaidCharacter::insert($newRows);
        $newRows = [];

        // Add instances
        $alreadyAdded = [];
        foreach (request()->input('instance_id') as $instanceId) {
            if ($instanceId && !isset($alreadyAdded[$instanceId])) {
                $alreadyAdded[$instanceId] = true;
                $newRows[] = [
                    'raid_id'     => $raid->id,
                    'instance_id' => $instanceId,
                ];
                $instanceCount++;
            }
        }
        RaidInstance::insert($newRows);
        $newRows = [];

        // Add raid groups
        $alreadyAdded = [];
        foreach (request()->input('raid_group_id') as $raidGroupId) {
            if ($raidGroupId && !isset($alreadyAdded[$raidGroupId])) {
                $alreadyAdded[$raidGroupId] = true;
                $newRows[] = [
                    'raid_id'       => $raid->id,
                    'raid_group_id' => $raidGroupId,
                ];
                $raidGroupCount++;
            }
        }
        RaidRaidGroup::insert($newRows);
        $newRows = [];

        // Add raid logs
        if (request()->input('logs')) {
            // Replace old logs with new ones
            $this->syncLogs(request()->input('logs'), $raid->logs, $raid->id);
        }

        AuditLog::create([
            'description'   => $currentMember->username . " created raid \"{$raid->name}\" with {$characterCount} character(s), {$instanceCount} dungeon(s), and {$raidGroupCount} raid group(s)",
            'member_id'     => $currentMember->id,
            'guild_id'      => $guild->id,
            'raid_group_id' => ($raidGroupCount ? request()->input('raid_group_id')[0] : null),
            'raid_id'       => $raid->id,
        ]);

        request()->session()->flash('status', "Successfully created Raid {$raid->name}.");
        return redirect()->route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]);
    }

    /**
     * Show a raid for editing, or creating if no ID is provided
     *
     * @return \Illuminate\Http\Response
     */
    public function showEdit($guildId, $guildSlug, $id, $copy = false) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

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
            $raid->load([
                'characters',
                'instances',
                'logs',
                'raidGroups'
            ]);
        }

        $guild->load([
            'allCharactersWithAttendance',
            'characters',
            'characters.secondaryRaidGroups',
            'raidGroups',
            'raidGroups.role',
        ]);

        $instances = Instance::where('expansion_id', $guild->expansion_id)->get();

        // When copying a raid, drop the raid's properties that we don't want copied over
        $originalRaid = null;
        if ($copy && $raid) {
            $originalRaid = clone $raid;
            $raid->id     = null;
            $raid->name   = $raid->name . ' Copy';
            $raid->cancelled_at    = null;
            $raid->archived_at     = null;
            $raid->logs_deprecated = null;
            $raid->member_id       = null;
            $raid->created_at      = null;
            $raid->updated_at      = null;

            $raid->logs = collect();
            $raid->characters->transform(function ($character) {
                $character->pivot->raid_id      = null;
                $character->pivot->is_exempt    = null;
                $character->pivot->credit       = 1;
                $character->pivot->remark_id    = null;
                $character->pivot->public_note  = null;
                $character->pivot->officer_note = null;
                $character->pivot->created_at   = null;
                $character->pivot->updated_at   = null;
                return $character;
            });
        }

        return view('raids.edit', [
            'copy'            => $copy,
            'currentMember'   => $currentMember,
            'guild'           => $guild,
            'instances'       => $instances,
            'maxCharacters'   => self::MAX_CHARACTERS,
            'maxInstances'    => self::MAX_INSTANCES,
            'maxRaidGroups'   => self::MAX_RAID_GROUPS,
            'originalRaid'    => $originalRaid,
            'raid'            => $raid,
            'showOfficerNote' => $showOfficerNote,
        ]);
    }

    /**
     * Show the raids list page.
     *
     * @return \Illuminate\Http\Response
     */
    public function list($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $showArchived = request()->input('show_archived');

        $instances = Cache::remember('instances:expansion:' . $guild->expansion_id,
            env('CACHE_INSTANCES_SECONDS', 600),
            function () use ($guild) {
                return Instance::where('expansion_id', $guild->expansion_id)->get();
        });

        $guild->load(['allCharacters', 'members', 'raidGroups', 'raidGroups.role']);

        $query = Raid::select([
                'raids.*',
                DB::raw('COUNT(DISTINCT `raid_characters`.`id`) AS `character_count`'),
                DB::raw('COUNT(DISTINCT `character_items`.`id`) AS `item_count`'),
            ])
            ->leftJoin('raid_characters', 'raid_characters.raid_id', '=', 'raids.id')
            ->leftJoin('character_items', 'character_items.raid_id', '=', 'raids.id')
            ->where('raids.guild_id', $guild->id)
            ->orderBy('raids.date', 'desc')
            ->with(['instances', 'member', 'raidGroups', 'raidGroups.role'])
            ->groupBy('raids.id');

        if ($showArchived) {
            $query = $query->whereNotNull('raids.archived_at');
        } else {
            $query = $query->whereNull('raids.archived_at');
        }

        if (!empty(request()->input('character_id'))) {
            $query = $query->leftJoin('raid_characters AS raid_characters2', 'raid_characters2.raid_id', '=', 'raids.id')
                ->where('raid_characters2.character_id', request()->input('character_id'));
        }

        if (!empty(request()->input('item_instance_id'))) {
            $query = $query
                ->join('raid_instances', 'raid_instances.raid_id', 'raids.id')
                ->where('raid_instances.instance_id', request()->input('item_instance_id'));
        }

        if (!empty(request()->input('member_id'))) {
            $query = $query->join('characters', 'characters.id', 'raid_characters.character_id')
                ->join('members', 'members.id', 'characters.member_id')
                ->where('members.id', request()->input('member_id'));
        }

        if (!empty(request()->input('raid_group_id'))) {
            $query = $query->join('raid_raid_groups', 'raid_raid_groups.raid_id', 'raids.id')
                ->where('raid_raid_groups.raid_group_id', request()->input('raid_group_id'));
        }

        // if (!empty(request()->input('item_id'))) {
        //     $query = $query->where('items.item_id', request()->input('item_id'));
        //     $resources[] = Item::find(request()->input('item_id'));
        // }

        $raids = $query->paginate(self::RESULTS_PER_PAGE);

        $showAssignLoot = false;
        if ($currentMember->hasPermission('edit.raid-loot')) {
            $showAssignLoot = true;
        }

        $showEdit = false;
        if ($currentMember->hasPermission('edit.raids')) {
            $showEdit = true;
        }

        return view('raids.list', [
            'currentMember'  => $currentMember,
            'guild'          => $guild,
            'instances'      => $instances,
            'raids'          => $raids,
            'showArchived'   => $showArchived,
            'showAssignLoot' => $showAssignLoot,
            'showEdit'       => $showEdit,
        ]);
    }

    /**
     * Show a raid
     *
     * @return \Illuminate\Http\Response
     */
    public function show($guildId, $guildSlug, $id, $raidSlug = null) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $raid = $guild->raids->find($id);

        if (!$raid) {
            abort(404, 'Raid not found.');
        }

        if ($raid->slug != $raidSlug) {
            return redirect()->route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]);
        }

        $showAssignLoot = false;
        if ($currentMember->hasPermission('edit.raid-loot')) {
            $showAssignLoot = true;
        }

        $showEditCharacter = false;
        if ($currentMember->hasPermission('edit.characters')) {
            $showEditCharacter = true;
        }

        $showEditCharacterLoot = false;
        if ($currentMember->hasPermission('loot.characters')) {
            $showEditCharacterLoot = true;
        }

        $showEditRaid = false;
        if ($currentMember->hasPermission('edit.raids')) {
            $showEditRaid = true;
        }

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $raid->load([
            'batches',
            'characters' => function ($query) use ($raid) {
                return $query->with([
                    'received' => function ($query) use ($raid) {
                        return $query->where([
                            'character_items.raid_id' => $raid->id
                        ]);
                    }
                ]);
            },
            'instances',
            'raidGroups',
            'raidGroups.role'
        ]);

        // TODO: Get this to also count items where the raid_id matches, but the batch.raid_id
        // was from a different raid. This means it was individually swapped over.
        // I couldn't get the quey to work without spending too much time, so I'm ignoring it.
        $manualItemAssignmentCount = CharacterItem::
            where([
                'character_items.raid_id' => $raid->id,
                'character_items.type' => 'received',
            ])
            ->whereNull('character_items.batch_id')
            ->groupBy('character_items.id')
            ->count();

        return view('raids.show', [
            'currentMember'             => $currentMember,
            'guild'                     => $guild,
            'manualItemAssignmentCount' => $manualItemAssignmentCount,
            'raid'                      => $raid,
            'remarks'                   => Raid::remarks(),
            'showAssignLoot'            => $showAssignLoot,
            'showEditCharacter'         => $showEditCharacter,
            'showEditCharacterLoot'     => $showEditCharacterLoot,
            'showEditRaid'              => $showEditRaid,
            'showOfficerNote'           => $showOfficerNote,
        ]);
    }

    /**
     * Update a raid
     * @return
     */
    public function update($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.raids')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit Raid Groups.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules = array_merge($this->getValidationRules($guild), [
            'id' => [
                'required',
                'integer',
                Rule::exists('raids', 'id')->where('raids.guild_id', $guild->id),
            ],
            'is_cancelled' => 'nullable|boolean',
            'is_archived'  => 'nullable|boolean',
        ]);

        $validationMessages = ['id' => 'Raid ID must match one of the raids in your guild.'];

        $this->validate(request(), $validationRules, $validationMessages);

        $raid = Raid::where([['id', request()->input('id')], ['guild_id', $guild->id]])->first();
        if (!$raid) {
            abort(404, 'Raid not found.');
        }

        $updateValues = [];

        $updateValues['date']         = request()->input('date');
        $updateValues['name']         = request()->input('name');
        $updateValues['public_note']  = request()->input('public_note');
        $updateValues['officer_note'] = request()->input('officer_note');
        $updateValues['logs_deprecated'] = request()->input('logs_deprecated');

        $updateValues['slug']         = slug(request()->input('name'));
        $updateValues['cancelled_at'] = request()->input('is_cancelled') && request()->input('is_cancelled') == 1 ? ($raid->cancelled_at ? $raid->cancelled_at : getDateTime()) : null;
        $updateValues['archived_at'] = request()->input('is_archived') && request()->input('is_archived') == 1 ? ($raid->is_archived ? $raid->is_archived : getDateTime()) : null;

        $raid->update($updateValues);

        $auditMessage = '';

        if ($updateValues['name'] != $raid->name) {
            $auditMessage .= ' (renamed to ' . $updateValues['name'] . ')';
        }

        if ($updateValues['cancelled_at'] != $raid->cancelled_at) {
            $auditMessage .= $updateValues['cancelled_at'] ? ' (cancelled)' : ' (un-cancelled)';
        }

        if ($updateValues['archived_at'] != $raid->archived_at) {
            $auditMessage .= $updateValues['archived_at'] ? ' (archived)' : ' (unarchived)';
        }

        // Sync characters
        $characters = $this->filterCharacterInputs(request()->input('characters'));
        $raid->characters()->sync($characters);

        // Sync instances
        $instances = $this->filterInstanceInputs(request()->input('instance_id'));
        $raid->instances()->sync($instances);

        if (request()->input('logs')) {
            // Replace old logs with new ones
            $this->syncLogs(request()->input('logs'), $raid->logs, $raid->id);
        }

        // Sync raid groups
        $raidGroups = $this->filterRaidGroupInputs(request()->input('raid_group_id'));
        $raid->raidGroups()->sync($raidGroups);

        AuditLog::create([
            'description' => $currentMember->username . " updated a Raid " . $auditMessage,
            'member_id'   => $currentMember->id,
            'guild_id'    => $guild->id,
            'raid_id'     => $raid->id,
        ]);

        request()->session()->flash('status', 'Successfully updated ' . $raid->name . '.');
        return redirect()->route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]);
    }

    // Removes duplicates, indexes array by ID.
    private function filterCharacterInputs($characterInputs) {
        $characterInputs = array_filter($characterInputs, function ($character) { return $character['character_id']; });
        $characters = [];

        foreach ($characterInputs as $character) {
            if (!isset($character['is_exempt'])) {
                $character['is_exempt'] = 0;
            }
            // This has the added effect of filtering out duplicates
            $characters[$character['character_id']] = $character;
        }
        return $characters;
    }

    // Removes duplicates, indexes array by ID.
    private function filterInstanceInputs($instanceInputs) {
        $instanceInputs = array_filter($instanceInputs, function ($instance) { return $instance; });
        $instances = [];

        foreach ($instanceInputs as $instance) {
            // This has the added effect of filtering out duplicates
            $instances[$instance] = $instance;
        }
        return $instances;
    }

    // Removes duplicates, indexes array by ID.
    private function filterRaidGroupInputs($raidGroupInputs) {
        $raidGroupInputs = array_filter($raidGroupInputs, function ($raidGroup) { return $raidGroup; });
        $raidGroups = [];

        foreach ($raidGroupInputs as $raidGroup) {
            // This has the added effect of filtering out duplicates
            $raidGroups[$raidGroup] = $raidGroup;
        }
        return $raidGroups;
    }

    private function syncLogs($newLogs, $oldLogs, $raidId) {
        $toAdd = [];
        $toDrop = [];

        foreach ($newLogs as $key => $newLog) {
            // Filter out any duplicates in the input
            foreach ($newLogs as $otherKey => $possibleDuplicate) {
                if ($key != $otherKey && $newLog['name'] == $possibleDuplicate['name']) {
                    unset($newLogs[$key]);
                    continue 2;
                }
            }

            $exists = false;
            foreach ($oldLogs as $oldLog) {
                if ($oldLog->name == $newLog['name']) {
                    $exists = true;
                }
            }

            if (!$exists && trim($newLog['name'])) {
                $toAdd[] = [
                    'name'    => $newLog['name'],
                    'raid_id' => $raidId,
                ];
                unset($newLogs[$key]);
            }
        }

        // See which logs no longer exist and need to be rmoved
        foreach ($oldLogs as $oldLog) {
            $found = false;
            foreach ($newLogs as $newLog) {
                if ($oldLog->name == $newLog['name']) {
                    $found = true;
                }
            }

            if (!$found) {
                $toDrop[] = $oldLog->id;
            }
        }

        Log::insert($toAdd);
        Log::whereIn('id', $toDrop)->delete();

        return true;
    }

    private function getValidationRules($guild) {
        return [
            'date'            => 'required|date_format:Y-m-d H:i:s',
            'name'            => 'required|string|max:75',
            'public_note'     => 'nullable|string|max:250',
            'officer_note'    => 'nullable|string|max:250',
            'logs_deprecated' => 'nullable|string|max:250',
            'logs.*.name'     => 'nullable|string|max:250',
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
            'characters.*.remark_id'    => ['nullable', 'integer', Rule::in(array_keys(Raid::remarks()))],
            'characters.*.credit'       => 'required|numeric|between:0,1',
            'characters.*.note'         => 'nullable|string|max:250',
            'characters.*.officer_note' => 'nullable|string|max:250',
        ];
    }
}
