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
        $createValues['logs']         = request()->input('logs');

        $createValues['slug']         = slug(request()->input('name'));
        $createValues['guild_id']     = $guild->id;
        $createValues['member_id']    = $currentMember->id;
        $createValues['cancelled_at'] = null;

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
        DB::table('raid_characters')->insert($newRows);
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
        DB::table('raid_instances')->insert($newRows);
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

        // TODO: Is this permission check ok? Different permission?
        if (!$currentMember->hasPermission('edit.raid-loot')) {
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
            $raid->load('characters', 'instances', 'raidGroups');
        }

        $guild->load([
            'characters',
            'raidGroups',
            'raidGroups.role']);

        $instances = Instance::where('expansion_id', $guild->expansion_id)->get();

        // When copying a raid, drop the raid's properties that we don't want copied over
        if ($copy && $raid) {
            $raid->original_id = $raid->id;
            $raid->id   = null;
            $raid->name = $raid->name . ' Copy';
            $raid->cancelled_at = null;
            $raid->logs         = null;
            $raid->member_id    = null;
            $raid->created_at   = null;
            $raid->updated_at   = null;
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
            'maxRaids'        => self::MAX_RAIDS,
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

        $guild->load(['characters', 'members', 'raidGroups', 'raidGroups.role']);

        $raids = Raid::select([
                'raids.*',
                DB::raw('COUNT(DISTINCT `raid_characters`.`character_id`) AS `character_count`'),
                DB::raw('COUNT(DISTINCT `character_items`.`item_id`) AS `item_count`'),
            ])
            ->leftJoin('raid_characters', 'raid_characters.raid_id', '=', 'raids.id')
            ->leftJoin('character_items', 'character_items.raid_id', '=', 'raids.id')
            ->where('raids.guild_id', $guild->id)
            ->orderBy('raids.date', 'desc')
            ->with(['instances', 'member', 'raidGroups', 'raidGroups.role'])
            ->groupBy('raids.id')
            ->paginate(self::RESULTS_PER_PAGE);

        $showEdit = false;
        if ($currentMember->hasPermission('edit.raids')) {
            $showEdit = true;
        }

        return view('raids.list', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raids'         => $raids,
            'showEdit'      => $showEdit,
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

        $raid->load('batches', 'characters', 'instances', /*'items',*/ 'raidGroups', 'raidGroups.role');

        return view('raids.show', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raid'          => $raid,
            'remarks'       => Raid::remarks(),
            'showEditCharacter'     => $showEditCharacter,
            'showEditCharacterLoot' => $showEditCharacterLoot,
            'showEditRaid'          => $showEditRaid,
            'showOfficerNote'       => $showOfficerNote,
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
            'cancelled_at' => 'nullable|date_format:Y-m-d H:i:s',
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
        $updateValues['logs']         = request()->input('logs');

        $updateValues['slug']         = slug(request()->input('name'));
        $updateValues['cancelled_at'] = request()->input('is_cancelled') && request()->input('is_cancelled') == 1 ? ($raid->cancelled_at ? $raid->cancelled_at : getDateTime()) : null;

        $raid->update($updateValues);

        $auditMessage = '';

        if ($updateValues['name'] != $raid->name) {
            $auditMessage .= ' (renamed to ' . $updateValues['name'] . ')';
        }

        if ($updateValues['cancelled_at'] != $raid->cancelled_at) {
            $auditMessage .= $updateValues['cancelled_at'] ? '(cancelled)' : '(un-cancelled)';
        }

        // Sync characters
        $characters = $this->filterCharacterInputs(request()->input('characters'));
        $raid->characters()->sync($characters);

        // Sync instances
        $instances = $this->filterInstanceInputs(request()->input('instance_id'));
        $raid->instances()->sync($instances);

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

    private function getValidationRules($guild) {
        return [
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
            'characters.*.remark_id'    => ['nullable', 'integer', Rule::in(array_keys(Raid::remarks()))],
            'characters.*.credit'       => 'required|numeric|between:0,1',
            'characters.*.note'         => 'nullable|string|max:250',
            'characters.*.officer_note' => 'nullable|string|max:250',
        ];
    }
}
