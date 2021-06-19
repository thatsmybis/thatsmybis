<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Item};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CharacterLootController extends Controller
{
    const MAX_RECEIVED_ITEMS = 200;
    const MAX_RECIPES        = 100;
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

    /**
     * Show a character's loot for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function loot($guildId, $guildSlug, $characterId, $nameSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $query = Character::select('characters.*')->where(['characters.id' => $characterId, 'characters.guild_id' => $guild->id]);
        $query = Character::addAttendanceQuery($query);
        $character = $query->firstOrFail();

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('loot.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit someone else\'s loot.');
            return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
        }

        $character = $character->load(['member', 'raidGroup', 'raidGroup.role', 'received', 'recipes', 'wishlist']);

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
        if (!$guild->is_wishlist_disabled && ($guild->is_wishlist_locked && !$currentMember->hasPermission('loot.characters'))) {
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
            'maxWishlistItems' => $guild->max_wishlist_items ? $guild->max_wishlist_items : self::MAX_WISHLIST_ITEMS,
        ]);
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

        if (!$guild->is_wishlist_disabled && (!$guild->is_wishlist_locked || $currentMember->hasPermission('loot.characters') || ($currentMember->id == $character->member_id && $currentMember->is_wishlist_unlocked))) {
            if (request()->input('wishlist')) {
                $maxWishlistItems = $guild->max_wishlist_items ? $guild->max_wishlist_items : self::MAX_WISHLIST_ITEMS;
                $this->syncItems($character->wishlist, array_slice(request()->input('wishlist'), 0, $maxWishlistItems), Item::TYPE_WISHLIST, $character, $currentMember, true);
            }
        }

        if (!$guild->is_received_locked || $currentMember->hasPermission('loot.characters') || ($currentMember->id == $character->member_id && $currentMember->is_received_unlocked)) {
            if (request()->input('received')) {
                // Don't bother enforcing an item limit here
                $this->syncItems($character->received, request()->input('received'), Item::TYPE_RECEIVED, $character, $currentMember, false);
            }
        }

        if (request()->input('recipes')) {
            // Don't bother enforcing an item limit here
            $this->syncItems($character->recipes, request()->input('recipes'), Item::TYPE_RECIPE, $character, $currentMember, false);
        }
        return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug, 'b' => 1]);
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
