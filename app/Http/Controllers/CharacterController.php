<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Content, Guild, Item, RaidGroup, Role, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CharacterController extends Controller
{
    const MAX_RECEIVED_ITEMS = 200;
    const MAX_RECIPES        = 50;
    const MAX_WISHLIST_ITEMS = 50;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser']);
    }

    private function getValidationRules($guild) {
        return [
            'member_id'     => 'nullable|integer|exists:members,id',
            'name'          => 'required|string|min:2|max:32',
            'level'         => 'nullable|integer|min:1|max:' . $guild->getMaxLevel(),
            'race'          => ['nullable', 'string', Rule::in(Character::races($guild->expansion_id))],
            'class'         => ['nullable', 'string', Rule::in(Character::classes($guild->expansion_id))],
            'spec'          => 'nullable|string|max:50',
            'profession_1'  => ['nullable', 'string', 'different:profession_2', Rule::in(Character::professions($guild->expansion_id))],
            'profession_2'  => ['nullable', 'string', 'different:profession_1', Rule::in(Character::professions($guild->expansion_id))],
            'rank'          => 'nullable|integer|min:1|max:14',
            'rank_goal'     => 'nullable|integer|min:1|max:14',
            'raid_group_id' => 'nullable|integer|exists:raid_groups,id',
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
        $createValues['profession_1']  = request()->input('profession_1');
        $createValues['profession_2']  = request()->input('profession_2');
        $createValues['rank']          = request()->input('rank');
        $createValues['rank_goal']     = request()->input('rank_goal');
        $createValues['raid_group_id'] = request()->input('raid_group_id');
        $createValues['public_note']   = request()->input('public_note');
        $createValues['is_alt']        = (request()->input('is_alt') == "1" ? true : false);

        // User is creating their own character
        if (isset($createValues['member_id']) && $createValues['member_id'] == $currentMember->id) {
            $createValues['personal_note'] = request()->input('personal_note');
            $createValues['order']         = request()->input('order');
        }

        $createValues['guild_id']      = $guild->id;

        $character = Character::create($createValues);

        AuditLog::create([
            'description'  => $currentMember->username . ' created a character',
            'member_id'    => $currentMember->id,
            'guild_id'     => $guild->id,
            'character_id' => $character->id,
        ]);

        request()->session()->flash('status', 'Successfully created ' . $createValues['name']
            . ', ' . (request()->input('level') ? 'level '
            Group. requestGroup()->input('level') : '')
            . ' ' . request()->input('race')
            . ' ' . request()->input('class'));

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
        ]);

        $character = $guild->allCharacters->first();

        if (!$character) {
            request()->session()->flash('status', 'Character not found.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load(['raidGroups', 'raidGroups.role']);

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else\'s character.');
            return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
        }

        if ($currentMember->hasPermission('edit.characters')) {
            $guild->load('members');
        }

        return view('character.edit', [
            'character'     => $character,
            'createMore'    => false,
            'currentMember' => $currentMember,
            'guild'         => $guild,
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
            request()->session()->flash('status', 'Could not find character.');
            return redirect()->route('home');
        }

        return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guildSlug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
    }

    /**
     * Show a character's loot for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function loot($guildId, $guildSlug, $characterId, $nameSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $character = Character::where(['id' => $characterId, 'guild_id' => $guild->id])->firstOrFail();

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('loot.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else\'s loot.');
            return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
        }

        $character = $character->load(['member', 'raidGroup', 'raidGroup.role', 'received', 'recipes', 'wishlist']);

        $showPrios = false;
        if (!$guild->is_prio_private || $currentMember->hasPermission('view.prios')) {
            $showPrios = true;
            $character = $character->load('prios');
        }

        $lockReceived   = false;
        $unlockReceived = false;
        if ($guild->is_received_locked && !$currentMember->hasPermission('loot.characters')) {
            if ($currentMember->id == $character->member_id && $currentMember->is_received_unlocked) {
                $unlockReceived = true;
            } else {
                $lockReceived = true;
            }
        }

        $lockWishlist   = false;
        $unlockWishlist = false;
        if ($guild->is_wishlist_locked && !$currentMember->hasPermission('loot.characters')) {
            if ($currentMember->id == $character->member_id && $currentMember->is_wishlist_unlocked) {
                $unlockWishlist = true;
            } else {
                $lockWishlist = true;
            }
        }

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        return view('character.loot', [
            'character'       => $character,
            'currentMember'   => $currentMember,
            'guild'           => $guild,

            'lockReceived'    => $lockReceived,
            'lockWishlist'    => $lockWishlist,
            'unlockReceived'  => $unlockReceived,
            'unlockWishlist'  => $unlockWishlist,
            'showOfficerNote' => $showOfficerNote,
            'showPrios'       => $showPrios,

            'maxReceivedItems' => self::MAX_RECEIVED_ITEMS,
            'maxRecipes'       => self::MAX_RECIPES,
            'maxWishlistItems' => self::MAX_WISHLIST_ITEMS,
        ]);
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

        $guild->load('allRaidGroups');

        $character = Character::where(['id' => $characterId, 'guild_id' => $guild->id])
            ->with([
                'member',
                'raidGroup',
                'raidGroup.role',
                'received',
                'recipes',
            ])->firstOrFail();

        $showPrios = false;
        if (!$guild->is_prio_private || $currentMember->hasPermission('view.prios')) {
            $showPrios = true;
            $character = $character->load('prios');
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_private || $character->member_id == $currentMember->id || $currentMember->hasPermission('view.wishlists')) {
            $showWishlist = true;
            $character = $character->load(['wishlist']);
        }

        $showEdit = false;
        if ($character->member_id == $currentMember->id || $currentMember->hasPermission('edit.characters')) {
            $showEdit = true;
        }

        $showEditLoot = false;
        if ($character->member_id == $currentMember->id || $currentMember->hasPermission('loot.characters')) {
            $showEditLoot = true;
        }

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        return view('character.show', [
            'character'        => $character,
            'currentMember'    => $currentMember,
            'guild'            => $guild,
            'showEdit'         => $showEdit,
            'showEditLoot'     => $showEditLoot,
            'showOfficerNote'  => $showOfficerNote,
            'showPrios'        => $showPrios,
            'showWishlist'     => $showWishlist,
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

        if ($currentMember->hasPermission('edit.characters')) {
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

        return view('character.edit', [
            'character'     => null,
            'createMore'    => $createMore,
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'memberId'      => $memberId,
        ]);
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
                return $query->Where('members.id', request()->input('member_id'));
            },
            'allCharacters' => function ($query) {
                return $query->whereRaw('LOWER(characters.name) COLLATE utf8mb4_bin = (?)', strtolower(request()->input('name')))
                    ->orWhere('id', request()->input('id'));
            },
            'raidGroups',
        ]);

        $character = $guild->allCharacters->where('id', request()->input('id'))->first();
        $sameNameCharacter = $guild->allCharacters->where('name', request()->input('name'))->first();

        if (!$character) {
            request()->session()->flash('status', 'Character not found.');
            return redirect()->back();
        }

        // Can't create a duplicate name
        if ($sameNameCharacter && ($character->id != $sameNameCharacter->id)) {
            request()->session()->flash('status', 'Name taken.');
            return redirect()->back();
        }

        $raidGroup = null;

        if (request()->input('raid_group_id')) {
            $raidGroup = $guild->raidGroups->where('id', request()->input('raid_group_id'))->first();
        }

        if (request()->input('raid_group_id') && !$raidGroup) {
            request()->session()->flash('status', 'Raid Group not found.');
            return redirect()->back();
        }

        $validationRules = $this->getValidationRules($guild);
        $validationRules['id'] = 'required|integer|exists:characters,id';

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $updateValues = [];

        // Can you edit someone else's character?
        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else\'s character.');
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

        $updateValues['name']          = request()->input('name');
        $updateValues['slug']          = slug(request()->input('name'));
        $updateValues['level']         = request()->input('level');
        $updateValues['race']          = request()->input('race');
        $updateValues['class']         = request()->input('class');
        $updateValues['spec']          = request()->input('spec');
        $updateValues['profession_1']  = request()->input('profession_1');
        $updateValues['profession_2']  = request()->input('profession_2');
        $updateValues['rank']          = request()->input('rank');
        $updateValues['rank_goal']     = request()->input('rank_goal');
        $updateValues['raid_group_id'] = request()->input('raid_group_id');
        $updateValues['public_note']   = request()->input('public_note');
        $updateValues['is_alt']        = (request()->input('is_alt') == "1" ? true : false);

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

        if ($updateValues['raid_group_id'] != $character->raid_group_id) {
            $auditMessage .= ' (changed Raid Group to ' . ($raidGroup ? $raidGroup->name : 'none') . ')';
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
                $auditMessage .= ' (made inactive)';
            } else {
                $auditMessage .= ' (made active)';
            }
        }

        $character->update($updateValues);

        AuditLog::create([
            'description'  => $currentMember->username . ' updated a character ' . ($auditMessage ? $auditMessage : ''),
            'member_id'    => $currentMember->id,
            'guild_id'     => $guild->id,
            'character_id' => $character->id,
        ]);

        request()->session()->flash('status', 'Successfully updated ' . $updateValues['name'] . ', ' . (request()->input('level') ? 'level ' . request()->input('level') : '') . ' ' . request()->input('race') . ' ' . request()->input('class'));

        return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
    }

    /**
     * Update a character's loot
     *
     * @return \Illuminate\Http\Response
     */
    public function updateLoot($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'allCharacters' => function ($query) {
                return $query->Where('id', request()->input('id'))
                    ->with(['wishlist', 'recipes', 'received']);
            },
        ]);

        $character = $guild->allCharacters->first();

        if (!$character) {
            abort(404, 'Character not found.');
        }

        $validationRules =  [
            'id'                     => 'required|integer|exists:characters,id',
            'wishlist.*.item_id'     => 'nullable|integer|exists:items,item_id',
            'wishlist.*.is_received' => 'nullable|boolean',
            'wishlist.*.is_offspec'  => 'nullable|boolean',
            'received.*.item_id' => 'nullable|integer|exists:items,item_id',
            'recipes.*.item_id'  => 'nullable|integer|exists:items,item_id',
            'public_note'        => 'nullable|string|max:140',
            'officer_note'       => 'nullable|string|max:140',
            'personal_note'      => 'nullable|string|max:2000',
        ];

        $this->validate(request(), $validationRules);

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('loot.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else\'s loot.');
            return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
        }

        $updateValues = [];

        $updateValues['public_note'] = request()->input('public_note');

        if ($currentMember->hasPermission('edit.officer-notes') && request()->input('officer_note')) {
            $updateValues['officer_note'] = request()->input('officer_note');
        }

        // User is editing their own character
        if ($character->member_id == $currentMember->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
            $updateValues['order']         = request()->input('order');
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

        if (!$guild->is_wishlist_locked || $currentMember->hasPermission('loot.characters') || ($currentMember->id == $character->member_id && $currentMember->is_wishlist_unlocked)) {
            if (request()->input('wishlist')) {
                $this->syncItems($character->wishlist, request()->input('wishlist'), Item::TYPE_WISHLIST, $character, $currentMember, true);
            }
        }

        if (!$guild->is_received_locked || $currentMember->hasPermission('loot.characters') || ($currentMember->id == $character->member_id && $currentMember->is_received_unlocked)) {
            if (request()->input('received')) {
                $this->syncItems($character->received, request()->input('received'), Item::TYPE_RECEIVED, $character, $currentMember, false);
            }
        }

        if (request()->input('recipes')) {
            $this->syncItems($character->recipes, request()->input('recipes'), Item::TYPE_RECIPE, $character, $currentMember, false);
        }
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
            abort(404, "Character not found.");
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
            abort(403, "You do not have permission to edit someone else's character.");
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

        request()->session()->flash('status', "Successfully updated " . $character->name ."'s note.");

        return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
    }

    /**
     * A custom sync function that allows for duplicate entries. I didn't see a clear way
     * to allow duplicates using Laravel's provided sync functions for collections. RIP.
     *
     * Heavy on the comments because my brain was having a hard time.
     *
     * If you want to see a much more succinct version using Laravel's sync() and some basic
     * PHP array checks, look at this file in commit 8064d3f09cfe52083e6ca5d288deb034251c9322
     *
     * @param Collection    $existingItems The items already attached to the character for this item type.
     * @param Array         $inputItems    The items provided from the HTML form input.
     * @param string        $itemType      The type of item. (ie. received, recipe, wishlist)
     * @param App\Character $character     The character to sync the items to.
     * @param App\Member    $currentMember The member syncing these items.
     * @param boolean       $updateFlags   Should we check for and update the OS and received flag?
     */
    private function syncItems($existingItems, $inputItems, $itemType, $character, $currentMember, $updateFlags) {
        $toAdd    = [];
        $toUpdate = [];
        $toDrop   = [];

        $now = getDateTime();

        $audits = [];
        $isReordered = false;

        /**
         * Go over all the items we already have in the database.
         * If any of these are found in the set sent from the input, we're going to update them with new metadata.
         * If any of these aren't found in the input, they shouldn't exist anymore so we'll drop them.
         */
        foreach ($existingItems as $existingItemKey => $existingItem) {
            $found = false;
            $i = 0;
            foreach ($inputItems as $inputItemKey => $inputItem) {
                if ($inputItem['item_id']) {
                    $i++;
                }
                // We found a match
                if (!isset($inputItems[$inputItemKey]['resolved']) && $existingItem->pivot->id == $inputItem['pivot_id']) {
                    $found = true;
                    $newValues = [];

                    if ($updateFlags) {
                        $inputItem['is_received'] = isset($inputItem['is_received']) ? 1 : 0;
                        $inputItem['is_offspec']  = isset($inputItem['is_offspec']) ? 1 : 0;
                    }

                    // Order changed
                    if ($existingItem->pivot->order != $i) {
                        $newValues['order']     = $i;
                        $newValues['old_order'] = $existingItem->pivot->order;
                        $isReordered = true;
                    }
                    // Is Received flag changed
                    if ($updateFlags && $existingItem->pivot->is_received != $inputItem['is_received']) {
                        if ($inputItem['is_received']) {
                            $newValues['is_received'] = 1;
                            $newValues['received_at'] = $now;
                        } else {
                            $newValues['is_received'] = 0;
                            $newValues['received_at'] = null;
                        }
                    }
                    // Is Offspec flag changed
                    if ($updateFlags && $existingItem->pivot->is_offspec != $inputItem['is_offspec']) {
                        if ($inputItem['is_offspec']) {
                            $newValues['is_offspec'] = 1;
                        } else {
                            $newValues['is_offspec'] = 0;
                        }
                    }

                    // At least one value changed
                    if (count($newValues)) {
                        $newValues['pivot_id'] = $existingItem->pivot->id;
                        $newValues['item_id']  = $existingItem->item_id;
                        $toUpdate[] = $newValues;
                    }
                    // Mark the input item as resolved so that we don't go over it again (we've already resolved what to do with this item)
                    $inputItems[$inputItemKey]['resolved'] = true;
                    break;
                }
            }

            // We didn't find this item in the input, so we should get rid of it
            if (!$found) {
                // We'll drop them all at once later on, rather than executing individual queries
                $toDrop[] = $existingItem->pivot->id;
                // Also remove it from the collection... for good measure I guess.
                $existingItems->forget($existingItemKey);

                $message = '';
                if (in_array($existingItem->pivot->type, [Item::TYPE_PRIO, Item::TYPE_WISHLIST])) {
                    $message = ' (rank ' . $existingItem->pivot->order . ')';
                }

                $audits[] = [
                    'description'  => $currentMember->username . ' removed item from a character (' . $itemType . ')' . $message,
                    'type'         => $itemType,
                    'member_id'    => $currentMember->id,
                    'guild_id'     => $currentMember->guild_id,
                    'character_id' => $character->id,
                    'item_id'      => $existingItem->item_id,
                    'created_at'   => $now,
                ];
            }
        }

        /**
         * Now we're left with just the items from the form that didn't already exist in the database.
         * We're going to add these to the database.
         */
        $i = 0;
        foreach ($inputItems as $inputItem) {
            if ($inputItem['item_id']) {
                $i++;
            }

            if (!isset($inputItem['resolved']) && $inputItem['item_id']) {
                $isReceived = isset($inputItem['is_received']) ? 1 : 0;
                $isOffspec  = isset($inputItem['is_offspec']) ? 1 : 0;

                $toAdd[] = [
                    'item_id'      => $inputItem['item_id'],
                    'is_received'  => $isReceived,
                    'is_offspec'   => $isOffspec,
                    'character_id' => $character->id,
                    'added_by'     => $currentMember->id,
                    'type'         => $itemType,
                    'order'        => $i,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];

                $audits[] = [
                    'description'  => $currentMember->username . ' added item to a character (' . $itemType . ')' . ($isReceived ? ' (received)' : null) . ($isOffspec ? ' (OS)' : null),
                    'type'         => $itemType,
                    'member_id'    => $currentMember->id,
                    'guild_id'     => $currentMember->guild_id,
                    'character_id' => $character->id,
                    'item_id'      => $inputItem['item_id'],
                    'created_at'   => $now,
                ];
            }
        }

        // Delete...
        DB::table('character_items')->whereIn('id', $toDrop)->delete();

        // Update...
        // I'm sure there's some clever way to perform an UPDATE statement with CASE statements... https://stackoverflow.com/questions/3432/multiple-updates-in-mysql
        // Don't have time for that just to remove a few queries.
        foreach ($toUpdate as $item) {
            $newValues = [];
            $auditMessage = '';

            // These keys only exist if we're changing them.
            if (isset($item['is_received'])) {
                $newValues['is_received'] = $item['is_received'];
                $auditMessage .= ($item['is_received'] ? 'set as received, ' : 'set as unreceived, ');
            }
            // Don't bother showing this until we have a manual received date input
            // if (isset($item['received_at'])) {
            //     $newValues['received_at'] = $item['received_at'];
            //     $auditMessage .= ($item['received_at'] ? 'added a received date, ' : 'removed received date, ');
            // }
            if (isset($item['is_offspec'])) {
                $newValues['is_offspec'] = $item['is_offspec'];
                $auditMessage .= ($item['is_offspec'] ? 'set as OS, ' : 'set as MS, ');
            }
            if (isset($item['order'])) {
                $newValues['order'] = $item['order'];
                if ($auditMessage) {
                    $auditMessage .= 'order ' . $item['old_order'] . ' -> ' . $item['order'] . ', ';
                }
            }
            $auditMessage = rtrim($auditMessage, ', ');

            $newValues['updated_at'] = $now;

            DB::table('character_items')
                ->where('id', $item['pivot_id'])
                ->update($newValues);

            // If we want to log EVERY prio change (this has a cascading effect and can result in hundreds of audits)
            // $audits[] = [
            //     'description'  => $currentMember->username . ' updated item on ' . $character->name . ' (' . $itemType . ')' . ' (prio set to ' . $item['order'] . ')',
            //     'type'         => $itemType,
            //     'member_id'    => $currentMember->id,
            //     'guild_id'     => $currentMember->guild_id,
            //     'character_id' => $character->id,
            //     'item_id'      => $item['pivot_id'],
            // ];

            if ($auditMessage) {
                $audits[] = [
                    'description'  => $currentMember->username . ' changed an item on ' . $character->name . ' (' . $itemType . '): ' . $auditMessage,
                    'type'         => $itemType,
                    'member_id'    => $currentMember->id,
                    'guild_id'     => $currentMember->guild_id,
                    'character_id' => $character->id,
                    'item_id'      => $item['item_id'],
                    'created_at'   => $now,
                ];
            }
        }

        if ($isReordered) {
            $audits[] = [
                'description'  => $currentMember->username . ' re-ordered items for a character (' . $itemType . ' items)',
                'type'         => $itemType,
                'member_id'    => $currentMember->id,
                'guild_id'     => $currentMember->guild_id,
                'character_id' => $character->id,
                'item_id'      => null,
                'created_at'   => $now,
            ];
        }

        // Insert...
        DB::table('character_items')->insert($toAdd);

        AuditLog::insert($audits);
    }
}
