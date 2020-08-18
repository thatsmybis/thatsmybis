<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Content, Guild, Item, Raid, Role, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use RestCord\DiscordClient;

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

    private function getValidationRules() {
        return [
            'member_id'     => 'nullable|integer|exists:members,id',
            'name'          => 'required|string|min:2|max:32',
            'level'         => 'nullable|integer|min:1|max:60',
            'race'          => ['nullable', 'string', Rule::in(Character::races())],
            'class'         => ['nullable', 'string', Rule::in(Character::classes())],
            'spec'          => 'nullable|string|max:50',
            'profession_1'  => ['nullable', 'string', 'different:profession_2', Rule::in(Character::professions())],
            'profession_2'  => ['nullable', 'string', 'different:profession_1', Rule::in(Character::professions())],
            'rank'          => 'nullable|integer|min:1|max:14',
            'rank_goal'     => 'nullable|integer|min:1|max:14',
            'raid_id'       => 'nullable|integer|exists:raids,id',
            'public_note'   => 'nullable|string|max:144',
            'officer_note'  => 'nullable|string|max:144',
            'personal_note' => 'nullable|string|max:2000',
            'order'         => 'nullable|integer|min:0|max:50',
            'is_inactive'   => 'nullable|boolean',
        ];
    }

    /**
     * Create a character
     * @return
     */
    public function create($guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');
        
        $guild->load([
            'members' => function ($query) use ($currentMember) {
                return $query->where('members.id', request()->input('member_id'))
                    ->orWhere('members.id', $currentMember->id);
            },
            'allCharacters' => function ($query) {
                return $query->where('characters.name', request()->input('name'));
            },
            'raids',
        ]);

        if ($guild->allCharacters->count() > 0) {
            abort(403, 'Name taken.');
        }

        $validationRules = $this->getValidationRules();

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $createValues = [];

        // Assign character to a different member
        if (request()->input('member_id') && (request()->input('member_id') != $currentMember->id) && $currentMember->hasPermission('edit.characters')) {
            $selectedMember = $guild->members->where('id', request()->input('member_id'))->first();

            if ($selectedMember) {
                $createValues['member_id'] = $selectedMember->id;
            }
        } else {
            $createValues['member_id'] = $currentMember->id;
        }

        if ($currentMember->hasPermission('edit.officer-notes')) {
            $createValues['officer_note'] = request()->input('officer_note');
        }

        $createValues['name']          = request()->input('name');
        $createValues['level']         = request()->input('level');
        $createValues['race']          = request()->input('race');
        $createValues['class']         = request()->input('class');
        $createValues['spec']          = request()->input('spec');
        $createValues['profession_1']  = request()->input('profession_1');
        $createValues['profession_2']  = request()->input('profession_2');
        $createValues['rank']          = request()->input('rank');
        $createValues['rank_goal']     = request()->input('rank_goal');
        $createValues['raid_id']       = request()->input('raid_id');
        $createValues['public_note']   = request()->input('public_note');

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

        request()->session()->flash('status', 'Successfully created ' . $createValues['name'] . ', ' . (request()->input('level') ? 'level ' . request()->input('level') : '') . ' ' . request()->input('race') . ' ' . request()->input('class'));

        return redirect()->route('character.show', ['guildSlug' => $guild->slug, 'name' => $character->name]);
    }

    /**
     * Show a character for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($guildSlug, $name)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $character = Character::where(['name' => $name, 'guild_id' => $guild->id])->with('member')->firstOrFail();

        $guild->load(['raids', 'raids.role']);

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else\'s character.');
            return redirect()->route('member.show', ['guildSlug' => $guild->slug, 'username' => $currentMember->username]);
        }

        if ($currentMember->hasPermission('edit.characters')) {
            $guild->load('members');
        }

        return view('character.edit', [
            'character'     => $character,
            'currentMember' => $currentMember,
            'guild'         => $guild,
        ]);
    }

    /**
     * Show a character's loot for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function loot($guildSlug, $name)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $character = Character::where(['name' => $name, 'guild_id' => $guild->id])->with(['member', 'raid', 'raid.role'])->firstOrFail();

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('loot.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else\'s loot.');
            return redirect()->route('member.show', ['guildSlug' => $guild->slug, 'username' => $currentMember->username]);
        }

        return view('character.loot', [
            'character'       => $character,
            'currentMember'   => $currentMember,
            'guild'           => $guild,

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
    public function show($guildSlug, $name)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $character = Character::where(['name' => $name, 'guild_id' => $guild->id])
            ->with([
                'member', 'raid', 'raid.role',
            ])->firstOrFail();

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
        ]);
    }

    /**
     * Show a character for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate($guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($currentMember->hasPermission('edit.characters')) {
            $guild->load('members');
        }

        return view('character.edit', [
            'character'     => null,
            'currentMember' => $currentMember,
            'guild'         => $guild,
        ]);
    }

    /**
     * Update a character
     * @return
     */
    public function update($guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'members' => function ($query) {
                return $query->Where('members.id', request()->input('member_id'));
            },
            'allCharacters' => function ($query) {
                return $query->where('name', request()->input('name'))
                    ->orWhere('id', request()->input('id'));
            },
            'raids',
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

        $raid = null;

        if (request()->input('raid_id')) {
            $raid = $guild->raids->where('id', request()->input('raid_id'))->first();
        }

        if (request()->input('raid_id') && !$raid) {
            request()->session()->flash('status', 'Raid not found.');
            return redirect()->back();
        }

        $validationRules = $this->getValidationRules();
        $validationRules['id'] = 'required|integer|exists:characters,id';

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $updateValues = [];

        // Can you edit someone else's character?
        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('edit.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else\'s character.');
            return redirect()->route('member.show', ['guildSlug' => $guild->slug, 'username' => $currentMember->username]);
        }

        // Can you change the character owner?
        if (request()->input('member_id') && $currentMember->hasPermission('edit.characters')) {
            $selectedMember = $guild->members->where('id', request()->input('member_id'))->first();
            $updateValues['member_id'] = ($selectedMember ? $selectedMember->id : null);
        }

        // Can you edit the officer notes?
        if ($currentMember->hasPermission('edit.officer-notes')) {
            $updateValues['officer_note'] = request()->input('officer_note');
        }

        $updateValues['name']         = request()->input('name');
        $updateValues['level']        = request()->input('level');
        $updateValues['race']         = request()->input('race');
        $updateValues['class']        = request()->input('class');
        $updateValues['spec']         = request()->input('spec');
        $updateValues['profession_1'] = request()->input('profession_1');
        $updateValues['profession_2'] = request()->input('profession_2');
        $updateValues['rank']         = request()->input('rank');
        $updateValues['rank_goal']    = request()->input('rank_goal');
        $updateValues['raid_id']      = request()->input('raid_id');
        $updateValues['public_note']  = request()->input('public_note');
        $updateValues['inactive_at']  = (request()->input('inactive_at') == 1 ? getDateTime() : null);

        // User is editing their own character
        if ($character->member_id == $currentMember->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
            $updateValues['order']         = request()->input('order');
        }

        $auditMessage = '';

        if ($updateValues['name'] != $character->name) {
            $auditMessage .= ' (renamed from ' . $character->name . ' to ' . $updateValues['name'] . ')';
        }

        if ($updateValues['raid_id'] != $character->raid_id) {
            $auditMessage .= ' (changed raid to ' . ($raid ? $raid->name : 'none') . ')';
        }

        if ($updateValues['public_note'] != $character->public_note) {
            $auditMessage .= ' (public note)';
        }

        if (isset($updateValues['officer_note']) && ($updateValues['officer_note'] != $character->officer_note)) {
            $auditMessage .= ' (officer note)';
        }

        $character->update($updateValues);

        AuditLog::create([
            'description'  => $currentMember->username . ' updated a character ' . ($auditMessage ? $auditMessage : ''),
            'member_id'    => $currentMember->id,
            'guild_id'     => $guild->id,
            'character_id' => $character->id,
        ]);

        request()->session()->flash('status', 'Successfully updated ' . $updateValues['name'] . ', ' . (request()->input('level') ? 'level ' . request()->input('level') : '') . ' ' . request()->input('race') . ' ' . request()->input('class'));

        return redirect()->route('character.show', ['guildSlug' => $guild->slug, 'name' => $character->name]);
    }

    /**
     * Update a character's loot
     *
     * @return \Illuminate\Http\Response
     */
    public function updateLoot($guildSlug)
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
            'id'                 => 'required|integer|exists:characters,id',
            'wishlist.*.item_id' => 'nullable|integer|exists:items,item_id',
            'received.*.item_id' => 'nullable|integer|exists:items,item_id',
            'recipes.*.item_id'  => 'nullable|integer|exists:items,item_id',
            'public_note'        => 'nullable|string|max:144',
            'officer_note'       => 'nullable|string|max:144',
            'personal_note'      => 'nullable|string|max:2000',
        ];

        $this->validate(request(), $validationRules);

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('loot.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else\'s loot.');
            return redirect()->route('member.show', ['guildSlug' => $guild->slug, 'username' => $currentMember->username]);
        }

        $updateValues = [];

        $updateValues['public_note']   = request()->input('public_note');

        if ($currentMember->hasPermission('edit.officer-notes') && request()->input('officer_note')) {
            $updateValues['officer_note']   = request()->input('officer_note');
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

        if (request()->input('wishlist')) {
            $this->syncItems($character->wishlist, request()->input('wishlist'), Item::TYPE_WISHLIST, $character, $currentMember);
        } else {
            $character->wishlist()->detach();
        }

        if (request()->input('received')) {
            $this->syncItems($character->received, request()->input('received'), Item::TYPE_RECEIVED, $character, $currentMember);
        } else {
            $character->received()->detach();
        }

        if (request()->input('recipes')) {
            $this->syncItems($character->recipes, request()->input('recipes'), Item::TYPE_RECIPE, $character, $currentMember);
        } else {
            $character->recipes()->detach();
        }

        return redirect()->route('character.show', ['guildSlug' => $guild->slug, 'name' => $character->name]);
    }

    /**
     * Update a character's note(s) only
     * @return
     */
    public function updateNote($guildSlug) {
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
            'public_note'   => 'nullable|string|max:144',
            'officer_note'  => 'nullable|string|max:144',
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

        return redirect()->route('character.show', ['guildSlug' => $guild->slug, 'name' => $character->name]);
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
     */
    private function syncItems($existingItems, $inputItems, $itemType, $character, $currentMember) {
        $toAdd    = [];
        $toUpdate = [];
        $toDrop   = [];

        $now = getDateTime();

        $audits = [];

        /**
         * Go over all the items we already have in the database.
         * If any of these are found in the set sent from the input, we're going to update them with new metadata.
         * If any of these aren't found in the input, they shouldn' exist anymore so we'll drop them.
         */
        foreach ($existingItems as $existingItemKey => $existingItem) {
            $found = false;
            $i = 0;
            foreach ($inputItems as $inputItemKey => $inputItem) {
                if ($inputItem['item_id']) {
                    $i++;
                }
                // We found a match
                if (!isset($inputItems[$inputItemKey]['resolved']) && $existingItem->item_id == $inputItem['item_id']) {
                    $found = true;
                    if ($existingItem->pivot->order != $i) {
                        // Update the metadata
                        $toUpdate[] = [
                            'id'    => $existingItem->pivot->id,
                            'order' => $i,
                        ];
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

                $audits[] = [
                    'description'  => $currentMember->username . ' removed item from a character (' . $itemType . ')' . ' (prio ' . $existingItem->pivot->order . ')',
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
                $toAdd[] = [
                    'item_id'      => $inputItem['item_id'],
                    'character_id' => $character->id,
                    'added_by'     => $currentMember->id,
                    'type'         => $itemType,
                    'order'        => $i,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];

                $audits[] = [
                    'description'  => $currentMember->username . ' added item to a character (' . $itemType . ')',
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
            DB::table('character_items')
                ->where('id', $item['id'])
                ->update([
                    'order'      => $item['order'],
                    'updated_at' => $now,
                ]);

            // If we want to log EVERY prio change (this has a cascading effect and can result in hundreds of audits)
            // $audits[] = [
            //     'description'  => $currentMember->username . ' updated item on ' . $character->name . ' (' . $itemType . ')' . ' (prio set to ' . $item['order'] . ')',
            //     'member_id'    => $currentMember->id,
            //     'guild_id'     => $currentMember->guild_id,
            //     'character_id' => $character->id,
            //     'item_id'      => $item['id'],
            // ];
        }

        if (count($toUpdate) > 0) {
            $audits[] = [
                'description'  => $currentMember->username . ' changed item priority(s) for a character (' . $itemType . ')',
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
