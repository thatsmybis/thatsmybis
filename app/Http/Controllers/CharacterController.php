<?php

namespace App\Http\Controllers;

use App\{Character, Content, Guild, Item, Raid, Role, User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use RestCord\DiscordClient;

class CharacterController extends Controller
{
    const MAX_RECEIVED_ITEMS = 100;
    const MAX_RECIPES        = 50;
    const MAX_WISHLIST_ITEMS = 16;

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
            'name'          => 'nullable|string|min:2|max:32',
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
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                    return $query->where('members.user_id', Auth::id())
                        ->orWhere('members.id', request()->input('member_id'));
                },
            'characters' => function ($query) {
                    return $query->where('name', request()->input('name'));
                },
            'raids',
            ])->firstOrFail();

        $currentMember  = $guild->members->where('user_id', Auth::id())->first();

        if ($guild->characters->count() > 0) {
            abort(403, 'Name taken.');
        }

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        // TODO: Validate user has permissions to create a character in this guild

        $validationRules = $this->getValidationRules();

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $createValues = [];

        // TODO: If has permissions, allow to create characters tied to other members
        // TODO: also if they can edit officer note
        if (true) {
            $selectedMember = $guild->members->where('id', request()->input('member_id'))->first();
            // if (!$selectedMember) {
            //     abort(403, 'Guild member not found.');
            // }

            if ($selectedMember) {
                $createValues['member_id']    = $selectedMember->id;
            }
            $createValues['officer_note'] = request()->input('officer_note');
        } else {
        // Assign to current user's member object for this guild
            $createValues['member_id'] = $currentMember->id;
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

        // User is editing their own character
        if (isset($createValues['member_id']) && $createValues['member_id'] == $currentMember->id) {
            $createValues['personal_note'] = request()->input('personal_note');
            $createValues['order']         = request()->input('order');
        }

        $createValues['guild_id']      = $guild->id;

        $character = Character::create($createValues);

        request()->session()->flash('status', 'Successfully created ' . $createValues['name'] . ', ' . (request()->input('level') ? 'level ' . request()->input('level') : '') . ' ' . request()->input('race') . ' ' . request()->input('class'));

        return redirect()->route('character.show', ['guildSlug' => $guild->slug, 'name' => $character->name]);
    }

    /**
     * Show a character for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($guildSlug, $id)
    {
        $guild = Guild::where('slug', $guildSlug)->with('members')->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        $character = Character::where(['id' => $id], ['guild_id' => $guild->id])->with('member')->first();

        if (!$character) {
            $character = Character::where(['name' => $id], ['guild_id' => $guild->id])->with('member')->firstOrFail();
        }

        // TODO: Validate user can edit this character in this guild

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
    public function loot($guildSlug, $id)
    {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                return $query->where('members.user_id', Auth::id());
            }])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        // TODO: Permissions

        $character = Character::where(['id' => $id], ['guild_id' => $guild->id])->with(['member', 'received', 'recipes', 'wishlist'])->first();

        if (!$character) {
            $character = Character::where(['name' => $id], ['guild_id' => $guild->id])->with('member')->firstOrFail();
        }

        // TODO: Validate user can edit this character's loot in this guild

        return view('character.loot', [
            'character'     => $character,
            'currentMember' => $currentMember,
            'guild'         => $guild,

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
    public function show($guildSlug, $id)
    {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                return $query->where('members.user_id', Auth::id());
            }])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        $character = Character::where(['id' => $id], ['guild_id' => $guild->id])->with('member')->first();

        if (!$character) {
            $character = Character::where(['name' => $id], ['guild_id' => $guild->id])->with('member')->firstOrFail();
        }

        // TODO: Validate user can view this character in this guild

        return view('character.show', [
            'character'        => $character,
            'currentMember'    => $currentMember,
            'guild'            => $guild,
            'showOfficerNote'  => false, // TODO permissions for this
            'showPersonalNote' => ($currentMember->id == $character->member_id),
        ]);
    }

    /**
     * Show a character for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with('members')->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        // TODO: Validate user can create a character in this guild

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
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                    return $query->where('members.user_id', Auth::id())
                        ->orWhere('members.id', request()->input('member_id'));
                },
            'characters' => function ($query) {
                    return $query->where('name', request()->input('name'))
                        ->orWhere('id', request()->input('id'));
                },
            'raids',
            ])->firstOrFail();

        $currentMember  = $guild->members->where('user_id', Auth::id())->first();
        $selectedMember = $guild->members->where('id', request()->input('member_id'))->first();

        $currentCharacter  = $guild->characters->where('id', request()->input('id'))->first();
        $sameNameCharacter = $guild->characters->where('name', request()->input('name'))->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        if (!$currentCharacter) {
            abort(404, 'Character not found.');
        }

        // Can't create a duplicate name
        if ($sameNameCharacter && ($currentCharacter->id != $sameNameCharacter->id)) {
            abort(403, 'Name taken.');
        }

        // TODO: Validate user has permissions to update this character in this guild

        $validationRules = $this->getValidationRules();
        $validationRules['id'] = 'required|integer|exists:characters,id';

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $updateValues = [];

        // TODO: If has permissions, allow to change who owns character or modify someone else's character
        // TODO: Also if they can edit officer note
        if (true) {
            // if (!$selectedMember) {
            //     abort(404, 'Guild member not found.');
            // }

            $updateValues['member_id']    = ($selectedMember ? $selectedMember->id : null);
            $updateValues['officer_note'] = request()->input('officer_note');
        } else if (!$selectedMember) {
            abort(403, "You do not have permissions to create a character that isn't assigned to a player.");
        } else if ($currentCharacter->member_id != $currentMember->id) {
            abort(403, "You do not have permission to edit someone else's character.");
        } else if ($selectedMember->id != $currentMember->id) {
            abort(403, 'You do not have permission to change who owns this character.');
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
        if ($currentCharacter->member_id == $currentMember->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
            $updateValues['order']         = request()->input('order');
        }

        $currentCharacter->update($updateValues);

        request()->session()->flash('status', 'Successfully updated ' . $updateValues['name'] . ', ' . (request()->input('level') ? 'level ' . request()->input('level') : '') . ' ' . request()->input('race') . ' ' . request()->input('class'));

        return redirect()->route('character.show', ['guildSlug' => $guild->slug, 'name' => $currentCharacter->name]);
    }

    /**
     * Update a character's loot
     *
     * @return \Illuminate\Http\Response
     */
    public function updateLoot($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                    return $query->where('members.user_id', Auth::id());
                },
            'characters' => function ($query) {
                    return $query->Where('id', request()->input('id'))
                        ->with(['wishlist', 'recipes', 'received']);
                },
            ])->firstOrFail();

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

        $currentMember = $guild->members->where('user_id', Auth::id())->first();
        $character = $guild->characters->first();

        if (!$character || !$currentMember) {
            abort(404, 'Character not found.');
        }

        // TODO: permissions; can user edit this character's loot?

        $updateValues = [];

        $updateValues['public_note']   = request()->input('public_note');

        // TODO: Permissions for who can edit officer note
        if (true) {
            $updateValues['officer_note']   = request()->input('officer_note');
        }

        // User is editing their own character
        if ($character->member_id == $currentMember->id) {
            $updateValues['personal_note'] = request()->input('personal_note');
            $updateValues['order']         = request()->input('order');
        }

        $character->update($updateValues);

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
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                return $query->where('members.user_id', Auth::id());
            },
            'characters' => function ($query) {
                return $query->where('id', request()->input('id'));
            },
            ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        $validationRules = $this->getValidationRules();
        $validationRules['id'] = 'required|integer|exists:characters,id';

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $character = $guild->characters->first();

        if (!$character) {
            abort(404, "Character not found.");
        }

        $updateValues = [];

        // TODO: If has permissions, allow to change who owns character or modify someone else's character
        // TODO: Also if they can edit officer note
        if (true) {
            $updateValues['officer_note'] = request()->input('officer_note');
        } else if ($currentMember->id != $character->member_id) {
            abort(403, "You do not have permission to edit someone else's character.");
        }

        $updateValues['public_note']   = request()->input('public_note');

        // User is editing their own character
        if ($currentMember->id == $character->member_id) {
            $updateValues['personal_note'] = request()->input('personal_note');
        }

        $character->update($updateValues);

        request()->session()->flash('status', "Successfully updated " . $character->name ."'s note.");

        return redirect()->route('character.show', ['guildSlug' => $guild->slug, 'name' => $character->name]);
    }

    /**
     * Remove a character
     * @return
     */
    public function remove($guildSlug) {
        // $guild = Guild::where('slug', $guildSlug)->firstOrFail();

        // // TODO: Validate user can update this character in this guild

        // $validationRules = [
        //     'id' => 'required|integer|exists:raids,id',
        // ];

        // $validationMessages = [];

        // $this->validate(request(), $validationRules, $validationMessages);

        // $guild = Guild::where('slug', $guildSlug)->firstOrFail();

        // $raid = Raid::where(['id' => request()->input('id'), 'guild_id' => $guild->id])->firstOrFail();

        // $raid->delete();

        // request()->session()->flash('status', 'Successfully removed raid.');
        // return redirect()->back();
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
                    // Update the metadata
                    $toUpdate[] = [
                        'id'    => $existingItem->pivot->id,
                        'order' => $i,
                    ];
                    $existingItem->pivot->order = $i;
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
        }

        // Insert...
        DB::table('character_items')->insert($toAdd);
    }
}
