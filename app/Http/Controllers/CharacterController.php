<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Raid, Role, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CharacterController extends Controller
{
    const MAX_RAID_GROUPS = 30;
    const MAX_CREATE_CHARACTERS_AT_ONCE = 15;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser']);
    }

    // These are more-or-less duplicated in submitCreateMany(). That one takes an array of characters.
    private function getValidationRules($guild) {
        return [
            'member_id'     => 'nullable|integer|exists:members,id',
            'name'          => 'required|string|min:2|max:32',
            'level'         => 'nullable|integer|min:1|max:' . $guild->getMaxLevel(),
            'race'          => ['nullable', 'string', Rule::in(array_keys(Character::races($guild->expansion_id)))],
            'class'         => ['nullable', 'string', Rule::in(array_keys(Character::classes($guild->expansion_id)))],
            'spec'          => ['nullable', 'string', Rule::in(array_keys(Character::specs($guild->expansion_id)))],
            'spec_label'    => 'nullable|string|min:1|max:50',
            'archetype'     => ['nullable', 'string', Rule::in(array_keys(Character::archetypes()))],
            'profession_1'  => ['nullable', 'string', 'different:profession_2', Rule::in(array_keys(Character::professions($guild->expansion_id)))],
            'profession_2'  => ['nullable', 'string', 'different:profession_1', Rule::in(array_keys(Character::professions($guild->expansion_id)))],
            'rank'          => 'nullable|integer|min:1|max:14',
            'rank_goal'     => 'nullable|integer|min:1|max:14',
            'raid_group_id' => [
                'nullable',
                'integer',
                Rule::exists('raid_groups', 'id')->where('raid_groups.guild_id', $guild->id),
            ],
            'raid_groups.*' => [
                'nullable',
                'integer',
                Rule::exists('raid_groups', 'id')->where('raid_groups.guild_id', $guild->id),
            ],
            'public_note'   => 'nullable|string|max:140',
            'officer_note'  => 'nullable|string|max:140',
            'personal_note' => 'nullable|string|max:2000',
            'order'         => 'nullable|integer|min:0|max:50',
            'inactive_at'   => 'nullable|boolean',
        ];
    }

    /**
     * Create a character
     * @return
     */
    public function create($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) use ($currentMember) {
                return $query->where('members.id', request()->input('member_id'))
                    ->orWhere('members.id', $currentMember->id);
            },
            'allCharacters' => function ($query) {
                // Use comparison that takes accents into account... as so many WoW characters have accented names.
                return $query->whereRaw('LOWER(characters.name) COLLATE utf8mb4_bin = (?)', strtolower(request()->input('name')));
            },
            'raidGroups',
        ]);

        if ($guild->allCharacters->count() > 0) {
            abort(403, 'Name taken.');
        }

        $validationRules = $this->getValidationRules($guild);

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $createValues = [];

        $canEditOthers = $currentMember->hasPermission('edit.characters');

        // Assign character to a different member
        if (request()->input('member_id') && (request()->input('member_id') != $currentMember->id) && $canEditOthers) {
            $selectedMember = $guild->members->where('id', request()->input('member_id'))->first();
            $createValues['member_id'] = null;
            if ($selectedMember) {
                $createValues['member_id'] = $selectedMember->id;
            }
        } else if (!request()->input('member_id') && $canEditOthers) {
            $createValues['member_id'] = null;
        } else {
            $createValues['member_id'] = $currentMember->id;
        }

        if ($currentMember->hasPermission('edit.officer-notes')) {
            $createValues['officer_note'] = request()->input('officer_note');
        }

        $createValues['name']          = request()->input('name');
        $createValues['slug']          = slug(request()->input('name'));
        $createValues['level']         = request()->input('level');
        $createValues['race']          = request()->input('race');
        $createValues['class']         = request()->input('class');
        $createValues['spec']          = request()->input('spec');
        $createValues['spec_label']    = request()->input('spec_label');
        $createValues['archetype']     = request()->input('archetype');
        $createValues['profession_1']  = request()->input('profession_1');
        $createValues['profession_2']  = request()->input('profession_2');
        $createValues['rank']          = request()->input('rank');
        $createValues['rank_goal']     = request()->input('rank_goal');
        $createValues['public_note']   = request()->input('public_note');
        $createValues['is_alt']        = (request()->input('is_alt') == "1" ? true : false);

        // User is creating their own character
        if (isset($createValues['member_id']) && $createValues['member_id'] == $currentMember->id) {
            $createValues['personal_note'] = request()->input('personal_note');
            $createValues['order']         = request()->input('order');
        }

        $createValues['guild_id']      = $guild->id;

        if (!$guild->is_raid_group_locked || $currentMember->hasPermission('edit.raids')) {
            $createValues['raid_group_id'] = request()->input('raid_group_id');
        }

        $character = Character::create($createValues);

        if (!$guild->is_raid_group_locked || $currentMember->hasPermission('edit.raids')) {
            $character->secondaryRaidGroups()->sync(array_unique(array_filter(request()->input('raid_groups'))));
        }

        AuditLog::create([
            'description'  => $currentMember->username . ' created a character',
            'member_id'    => $currentMember->id,
            'guild_id'     => $guild->id,
            'character_id' => $character->id,
        ]);

        request()->session()->flash('status',
            __('Successfully created :name, :level:race:class',
                [
                    'name'  => $createValues['name'],
                    'level' => (request()->input('level') ? __('level :number ', ['number' => request()->input('level')]) : ''),
                    'race'  => request()->input('race') ? request()->input('race') . ' ' : '',
                    'class' => request()->input('class') ? request()->input('class') . ' ' : '',
                ]
            )
        );

        if (request()->input('create_more')) {
            return redirect()->route('character.create', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'create_more' => 1]);
        } else {
            return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
        }
    }

    /**
     * Show a character for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($guildId, $guildSlug, $characterId, $nameSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'allCharacters' => function ($query) use($characterId) {
                return $query->where('characters.id', $characterId)
                ->with([
                    'member',
                ]);
            },
            'raidGroups',
            'raidGroups.role',
            'allRaidGroups',
            'allRaidGroups.role',
        ]);

        $character = $guild->allCharacters->first();

        if (!$character) {
            request()->session()->flash('status', __('Character not found.'));
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load(['raidGroups', 'raidGroups.role']);

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', __("You don't have permissions to edit someone else's character."));
            return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
        }

        if ($currentMember->hasPermission('edit.characters')) {
            $guild->load('members');
        }

        return view('character.edit', [
            'character'      => $character,
            'createMore'     => false,
            'currentMember'  => $currentMember,
            'editRaidGroups' => (!$guild->is_raid_group_locked || $currentMember->hasPermission('edit.raids')),
            'guild'          => $guild,
            'maxRaidGroups'  => self::MAX_RAID_GROUPS,
        ]);
    }

    /**
     * Find a character by ID.
     *
     * @param int $id The ID of the guild to find.
     *
     * @return \Illuminate\Http\Response
     */
    public function find($guildId, $guildSlug, $nameSlug)
    {
        $guild     = request()->get('guild');
        $character = Character::select(['id', 'slug'])->where(['slug' => $nameSlug, 'guild_id' => $guild->id])->first();

        if (!$character) {
            request()->session()->flash('status', __('Could not find character.'));
            return redirect()->route('home');
        }

        return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guildSlug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
    }

    /**
     * Show a character
     *
     * @return \Illuminate\Http\Response
     */
    public function show($guildId, $guildSlug, $characterId, $nameSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raidGroups', 'allRaidGroups']);

        $cacheKey = 'character:' . $characterId . ':guild:' . $guild->id . ':attendance:' . $guild->is_attendance_hidden;

        if (request()->get('bustCache')) {
            Cache::forget($cacheKey);
        }

        $character = Cache::remember($cacheKey, env('CACHE_CHARACTER_SECONDS', 5), function () use ($guild, $characterId) {
            $query = Character::select('characters.*')
                ->where(['characters.id' => $characterId, 'characters.guild_id' => $guild->id])
                ->with([
                    'member',
                    'raidGroup',
                    'raidGroup.role',
                    'raids',
                    'received',
                    'recipes',
                    'secondaryRaidGroups',
                    'secondaryRaidGroups.role',
                ]);

            if (!$guild->is_attendance_hidden) {
                $query = Character::addAttendanceQuery($query, $guild->id);
            }

            return $query->firstOrFail();
        });

        $showPrios = false;
        if (!$guild->is_prio_disabled && (!$guild->is_prio_private || $currentMember->hasPermission('view.prios'))) {
            $showPrios = true;
            if ($guild->prio_show_count && !$currentMember->hasPermission('view.prios')) {
                $character = $character->load(['prios' => function ($query) use ($guild) {
                    return $query->where([
                        ['character_items.order', '<=', $guild->prio_show_count],
                    ]);
                }]);
            } else {
                $character = $character->load('prios');
            }
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_disabled && (!$guild->is_wishlist_private || $character->member_id == $currentMember->id || $currentMember->hasPermission('view.wishlists'))) {
            $showWishlist = true;
            $character = $character->load(['allWishlists']);
        }

        $showEdit = false;
        if ($character->member_id == $currentMember->id || $currentMember->hasPermission('edit.characters')) {
            $showEdit = true;
        }

        $showEditLoot = false;
        if ($character->member_id == $currentMember->id || $currentMember->hasPermission('loot.characters')) {
            $showEditLoot = true;
        }

        $viewOfficerNotePermission = false;
        if ($currentMember->hasPermission('view.officer-notes')) {
            $viewOfficerNotePermission = true;
        }

        $editOfficerNotePermission = false;
        if ($currentMember->hasPermission('edit.officer-notes')) {
            $showOfficerNotePermission = true;
        }

        return view('character.show', [
            'character'       => $character,
            'currentMember'   => $currentMember,
            'guild'           => $guild,
            'remarks'         => Raid::remarks(),
            'showEdit'        => $showEdit,
            'showEditLoot'    => $showEditLoot,
            'showPrios'       => $showPrios,
            'showWishlist'    => $showWishlist,
            'editOfficerNotePermission' => $editOfficerNotePermission,
            'viewOfficerNotePermission' => $viewOfficerNotePermission,
        ]);
    }

    /**
     * Show a character for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $hasEditCharactersPermission = false;
        if ($currentMember->hasPermission('edit.characters')) {
            $hasEditCharactersPermission = true;
            $guild->load('members');
        }

        $createMore = false;
        if (request()->input('create_more')) {
            $createMore = true;
        }

        $memberId = null;
        if (request()->input('member_id')) {
            $memberId = request()->input('member_id');
        }

        $guild->load([
            'raidGroups',
            'raidGroups.role',
            'allRaidGroups',
            'allRaidGroups.role',
        ]);

        return view('character.edit', [
            'character'      => null,
            'createMore'     => $createMore,
            'currentMember'  => $currentMember,
            'editRaidGroups' => (!$guild->is_raid_group_locked || $currentMember->hasPermission('edit.raids')),
            'guild'          => $guild,
            'hasEditCharactersPermission' => $hasEditCharactersPermission,
            'memberId'       => $memberId,
            'maxRaidGroups'  => self::MAX_RAID_GROUPS,
        ]);
    }

    /**
     * Show a the page for creating a bunch of characters
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreateMany($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', __("You don't have permissions to do that."));
            return redirect()->back();
        }

        $guild->load([
            'characters',
            'members',
            'raidGroups',
            'raidGroups.role',
            'allRaidGroups',
            'allRaidGroups.role',
        ]);

        return view('character.massEdit', [
            'currentMember'  => $currentMember,
            'editRaidGroups' => true,
            'guild'          => $guild,
            'maxCharacters'  => self::MAX_CREATE_CHARACTERS_AT_ONCE,
            'maxRaidGroups'  => 4, // Default is more than is needed on this page
        ]);
    }

    /**
     * Create many a character
     * @return
     */
    public function submitCreateMany($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        // Validate
        $validationRules = [
            'character.*.member_id'     => 'nullable|integer|exists:members,id',
            'character.*.name'          => 'nullable|string|min:2|max:32',
            'character.*.name'          => [
                'required',
                'string',
                'min:2',
                'max:32',
                Rule::unique('characters')->where('guild_id', $guild->id),
            ],
            'character.*.level'         => 'nullable|integer|min:1|max:' . $guild->getMaxLevel(),
            'character.*.race'          => ['nullable', 'string', Rule::in(array_keys(Character::races($guild->expansion_id)))],
            'character.*.class'         => ['nullable', 'string', Rule::in(array_keys(Character::classes($guild->expansion_id)))],
            'character.*.spec'          => ['nullable', 'string', Rule::in(array_keys(Character::specs($guild->expansion_id)))],
            'character.*.spec_label'    => 'nullable|string|min:1|max:50',
            'character.*.archetype'     => ['nullable', 'string', Rule::in(array_keys(Character::archetypes()))],
            'character.*.profession_1'  => ['nullable', 'string', 'different:profession_2', Rule::in(array_keys(Character::professions($guild->expansion_id)))],
            'character.*.profession_2'  => ['nullable', 'string', 'different:profession_1', Rule::in(array_keys(Character::professions($guild->expansion_id)))],
            'character.*.rank'          => 'nullable|integer|min:1|max:14',
            'character.*.rank_goal'     => 'nullable|integer|min:1|max:14',
            'character.*.raid_group_id' => [
                'nullable',
                'integer',
                Rule::exists('raid_groups', 'id')->where('raid_groups.guild_id', $guild->id),
            ],
            'character.*.raid_groups.*' => [
                'nullable',
                'integer',
                Rule::exists('raid_groups', 'id')->where('raid_groups.guild_id', $guild->id),
            ],
            'character.*.public_note'   => 'nullable|string|max:140',
            'character.*.officer_note'  => 'nullable|string|max:140',
            'character.*.personal_note' => 'nullable|string|max:2000',
            'character.*.order'         => 'nullable|integer|min:0|max:50',
            'character.*.inactive_at'   => 'nullable|boolean',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $characters = [];

        foreach (request()->input('characters') as $inputCharacter) {
            if ($inputCharacter['name']) {
                $character = new Character;
                $character->name         = $inputCharacter['name'];
                $character->slug         = slug($inputCharacter['name']);
                $character->level        = array_key_exists('level', $inputCharacter)        ? $inputCharacter['level'] : null;
                $character->race         = array_key_exists('race', $inputCharacter)         ? $inputCharacter['race'] : null;
                $character->class        = array_key_exists('class', $inputCharacter)        ? $inputCharacter['class'] : null;
                $character->spec         = array_key_exists('spec', $inputCharacter)         ? $inputCharacter['spec'] : null;
                $character->spec_label   = array_key_exists('spec_label', $inputCharacter)   ? $inputCharacter['spec_label'] : null;
                $character->archetype    = array_key_exists('archetype', $inputCharacter)    ? $inputCharacter['archetype'] : null;
                $character->profession_1 = array_key_exists('profession_1', $inputCharacter) ? $inputCharacter['profession_1'] : null;
                $character->profession_2 = array_key_exists('profession_2', $inputCharacter) ? $inputCharacter['profession_2'] : null;
                $character->rank         = array_key_exists('rank', $inputCharacter)         ? $inputCharacter['rank'] : null;
                $character->rank_goal    = array_key_exists('rank_goal', $inputCharacter)    ? $inputCharacter['rank_goal'] : null;
                $character->public_note  = array_key_exists('public_note', $inputCharacter)  ? $inputCharacter['public_note'] : null;
                $character->officer_note = array_key_exists('officer_note', $inputCharacter) ? $inputCharacter['officer_note'] : null;
                $character->is_alt       = (array_key_exists('is_alt', $inputCharacter) && $inputCharacter['is_alt'] == "1" ? true : false);
                $character->guild_id     = $guild->id;
                $character->raid_group_id = array_key_exists('raid_group_id', $inputCharacter) ? $inputCharacter['raid_group_id'] : null;

                $character->save();

                AuditLog::create([
                    'description'  => $currentMember->username . ' created a character',
                    'member_id'    => $currentMember->id,
                    'guild_id'     => $guild->id,
                    'character_id' => $character->id,
                ]);

                if (array_key_exists('raid_groups', $inputCharacter) && count($inputCharacter['raid_groups'])) {
                    $character->secondaryRaidGroups()->sync(array_unique(array_filter($inputCharacter['raid_groups'])));
                }

                $characters[] = $character;
            }
        }

        request()->session()->flash('status', __('Successfully created :count characters.', ['count' => count($characters)]));
        return redirect()->route('character.showCreateMany', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]);
    }

    /**
     * Update a character
     * @return
     */
    public function update($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) {
                return $query->where('members.id', request()->input('member_id'));
            },
            'allCharacters' => function ($query) {
                return $query->whereRaw('LOWER(characters.name) COLLATE utf8mb4_bin = (?)', strtolower(request()->input('name')))
                    ->orWhere('id', request()->input('id'));
            },
            'raidGroups',
        ]);

        $character = $guild->allCharacters->where('id', request()->input('id'))->first();
        $character->load('secondaryRaidGroups');
        $sameNameCharacter = $guild->allCharacters->where('name', request()->input('name'))->first();

        if (!$character) {
            request()->session()->flash('status', __('Character not found.'));
            return redirect()->back();
        }

        // Can't create a duplicate name
        if ($sameNameCharacter && ($character->id != $sameNameCharacter->id)) {
            request()->session()->flash('status', __('Name taken.'));
            return redirect()->back();
        }

        $raidGroup = null;

        if (request()->input('raid_group_id')) {
            $raidGroup = $guild->raidGroups->where('id', request()->input('raid_group_id'))->first();
        }

        if (request()->input('raid_group_id') && !$raidGroup) {
            request()->session()->flash('status', __('Raid Group not found.'));
            return redirect()->back();
        }

        $validationRules = $this->getValidationRules($guild);
        $validationRules['id'] = 'required|integer|exists:characters,id';

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $updateValues = [];

        // Can you edit someone else's character?
        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', __("You don't have permissions to edit someone else's character."));
            return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
        }

        $canEditOthers = $currentMember->hasPermission('edit.characters');
        $selectedMember = null;

        // Assign character to a different member
        if (request()->input('member_id') && (request()->input('member_id') != $currentMember->id) && $canEditOthers) {
            $selectedMember = $guild->members->where('id', request()->input('member_id'))->first();
            $updateValues['member_id'] = null;
            if ($selectedMember) {
                $updateValues['member_id'] = $selectedMember->id;
            }
        } else if (!request()->input('member_id') && $canEditOthers) {
            $updateValues['member_id'] = null;
        } else {
            $selectedMember = $currentMember;
            $updateValues['member_id'] = $currentMember->id;
        }

        // Can you edit the officer notes?
        if ($currentMember->hasPermission('edit.officer-notes')) {
            $updateValues['officer_note'] = request()->input('officer_note');
        }

        $updateValues['name']         = request()->input('name');
        $updateValues['slug']         = slug(request()->input('name'));
        $updateValues['level']        = request()->input('level');
        $updateValues['race']         = request()->input('race');
        $updateValues['class']        = request()->input('class');
        $updateValues['spec']         = request()->input('spec');
        $updateValues['spec_label']   = request()->input('spec_label');
        $updateValues['archetype']    = request()->input('archetype');
        $updateValues['profession_1'] = request()->input('profession_1');
        $updateValues['profession_2'] = request()->input('profession_2');
        $updateValues['rank']         = request()->input('rank');
        $updateValues['rank_goal']    = request()->input('rank_goal');
        $updateValues['public_note']  = request()->input('public_note');
        $updateValues['is_alt']       = (request()->input('is_alt') == "1" ? true : false);

        // Cannot make inactive if already inactive
        if (($currentMember->hasPermission('inactive.characters') || $currentMember->id == $character->member_id) &&
            ((request()->input('inactive_at') == 1 && !$character->inactive_at) || (!request()->input('inactive_at') && $character->inactive_at))
        ) {
            $updateValues['inactive_at'] = (request()->input('inactive_at') == 1 ? getDateTime() : null);
        }

        // User is editing their own character
        if ($character->member_id == $currentMember->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
            $updateValues['order']         = request()->input('order');
        }

        $auditMessage = '';

        if ($updateValues['name'] != $character->name) {
            $auditMessage .= ' (renamed from ' . $character->name . ' to ' . $updateValues['name'] . ')';
        }

        if (array_key_exists('member_id', $updateValues) && $updateValues['member_id'] != $character->member_id) {
            $auditMessage .= ' (changed owner to ' . ($selectedMember ? $selectedMember->username : 'NONE') . ')';
        }

        if ($updateValues['public_note'] != $character->public_note) {
            $auditMessage .= ' (public note)';
        }

        if (array_key_exists('officer_note', $updateValues) && ($updateValues['officer_note'] != $character->officer_note)) {
            $auditMessage .= ' (officer note)';
        }

        if (array_key_exists('inactive_at', $updateValues)) {
            if ($updateValues['inactive_at']) {
                $auditMessage .= ' (archived)';
            } else {
                $auditMessage .= ' (unarchived)';
            }
        }

        if (!$guild->is_raid_group_locked || $currentMember->hasPermission('edit.raids')) {
            $updateValues['raid_group_id'] = request()->input('raid_group_id');

            if ($updateValues['raid_group_id'] != $character->raid_group_id) {
                $auditMessage .= ' (changed main raid group to ' . ($raidGroup ? $raidGroup->name : 'none') . ')';
            }
        }

        $character->update($updateValues);

        if (!$guild->is_raid_group_locked || $currentMember->hasPermission('edit.raids')) {
            $oldRaidGroups = $character->secondaryRaidGroups->keyBy('id')->keys()->toArray();
            // Drop duplicates and nulls
            $newRaidGroups = array_unique(array_filter(request()->input('raid_groups')));

            sort($oldRaidGroups);
            sort($newRaidGroups);

            if ($oldRaidGroups != $newRaidGroups) {
                $character->secondaryRaidGroups()->sync($newRaidGroups);
                $auditMessage .= ' (changed general raid groups [' . count($oldRaidGroups) . ' -> ' . count($newRaidGroups) . ' groups])';
            }
        }

        AuditLog::create([
            'description'  => $currentMember->username . ' updated a character ' . ($auditMessage ? $auditMessage : ''),
            'member_id'    => $currentMember->id,
            'guild_id'     => $guild->id,
            'character_id' => $character->id,
        ]);

        request()->session()->flash('status',
            __('Successfully updated :name, :level:race:class',
                [
                    'name'  => $updateValues['name'],
                    'level' => (request()->input('level') ? __('level :number ', ['number' => request()->input('level')]) : ''),
                    'race'  => request()->input('race') ? request()->input('race') . ' ' : '',
                    'class' => request()->input('class') ? request()->input('class') . ' ' : '',
                ]
            )
        );

        return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
    }

    /**
     * Update a character's note(s) only
     * @return
     */
    public function updateNote($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'allCharacters' => function ($query) {
                return $query->where('id', request()->input('id'));
            },
        ]);

        $character = $guild->allCharacters->first();

        if (!$character) {
            abort(404, __('Character not found.'));
        }

        $validationRules = [
            'id' => 'required|integer|exists:characters,id',
            'public_note'   => 'nullable|string|max:140',
            'officer_note'  => 'nullable|string|max:140',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $updateValues = [];

        if ($currentMember->hasPermission('edit.officer-notes')) {
            $updateValues['officer_note'] = request()->input('officer_note');
        } else if ($currentMember->id != $character->member_id) {
            abort(403, __("You do not have permission to edit someone else's character."));
        }

        $updateValues['public_note']   = request()->input('public_note');

        // User is editing their own character
        if ($currentMember->id == $character->member_id) {
            $updateValues['personal_note'] = request()->input('personal_note');
        }

        $auditMessage = '';

        if ($updateValues['public_note'] != $character->public_note) {
            $auditMessage .= ' (public note)';
        }

        if (isset($updateValues['officer_note']) && ($updateValues['officer_note'] != $character->officer_note)) {
            $auditMessage .= ' (officer note)';
        }

        $character->update($updateValues);

        if ($auditMessage) {
            AuditLog::create([
                'description'  => $currentMember->username . ' updated a character\'s notes' . ($auditMessage ? $auditMessage : ''),
                'member_id'    => $currentMember->id,
                'guild_id'     => $guild->id,
                'character_id' => $character->id,
            ]);
        }

        request()->session()->flash('status', __("Successfully updated :name's note.", ['name' => $character->name]));

        return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug, 'b' => 1]);
    }
}
