<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, CharacterItem, Item};
use App\Http\Controllers\AssignLootController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CharacterLootController extends Controller
{
    const MAX_RECEIVED_ITEMS = 400;
    const MAX_RECIPES        = 200;
    const MAX_WISHLIST_ITEMS = 100;
    const MAX_WISHLIST_LISTS = 10;

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

        $wishlistNumber = (int)request()->get('wishlist_number');

        if (!$wishlistNumber || $wishlistNumber < 1 || $wishlistNumber > self::MAX_WISHLIST_LISTS) {
            $wishlistNumber = $guild->current_wishlist_number;
        }

        $query = Character::select('characters.*')->where(['characters.id' => $characterId, 'characters.guild_id' => $guild->id]);
        $query = Character::addAttendanceQuery($query, $guild->id);
        $character = $query->firstOrFail();

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('loot.characters')) {
            request()->session()->flash('status', __("You don't have permissions to edit someone else's loot."));
            return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
        }

        $character = $character->load([
            'member',
            'raidGroup',
            'raidGroup.role',
            'received',
            'recipes',
            'allWishlists' => function ($query) use ($wishlistNumber) {
                return $query->where('character_items.list_number', $wishlistNumber);
            },
        ]);

        $character->setRelation('wishlist', $character->allWishlists->values());

        $guild->load(['raids' => function ($query) {
            return $query->limit(AssignLootController::RAID_HISTORY_LIMIT);
        }]);

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
            if (
                $currentMember->id == $character->member_id
                && ($currentMember->is_wishlist_unlocked || in_array($wishlistNumber, $guild->getWishlistLockedExceptions()))
            ) {
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
            'maxWishlistLists' => self::MAX_WISHLIST_LISTS,

            'wishlistNumber'   => $wishlistNumber,
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

        $validationRules =  [
            'id'                     => 'required|integer|exists:characters,id',
            'wishlist_number'        => "required|integer|min:1|max:{self::MAX_WISHLIST_LISTS}",
            'wishlist.*.item_id'     => 'nullable|integer|exists:items,item_id',
            'wishlist.*.is_received' => 'nullable|boolean',
            'wishlist.*.is_offspec'  => 'nullable|boolean',
            'wishlist.*.note'        => 'nullable|string|max:140',
            'received.*.item_id'     => 'nullable|integer|exists:items,item_id',
            'received.*.is_offspec'  => 'nullable|boolean',
            'received.*.note'        => 'nullable|string|max:140',
            'received.*.new_received_at' => 'nullable|date|before:tomorrow|after:2004-09-22',
            'received.*.new_raid_id'    => [
                'nullable',
                'integer',
                Rule::exists('raids', 'id')->where('raids.guild_id', $guild->id),
            ],
            'recipes.*.item_id'  => 'nullable|integer|exists:items,item_id',
            'recipes.*.note'     => 'nullable|string|max:140',
            'public_note'        => 'nullable|string|max:140',
            'officer_note'       => 'nullable|string|max:140',
            'personal_note'      => 'nullable|string|max:2000',
        ];

        $this->validate(request(), $validationRules);

        $guild->load([
            'allCharacters' => function ($query) {
                return $query->Where('id', request()->input('id'))
                    ->with([
                        'allWishlists' => function ($query) {
                            return $query->where('character_items.list_number', request()->input('wishlist_number'));
                        },
                        'recipes',
                        'received'
                    ]);
            },
        ]);

        $character = $guild->allCharacters->first();

        if (!$character) {
            abort(404, 'Character not found.');
        }

        if ($character->member_id != $currentMember->id && !$currentMember->hasPermission('loot.characters')) {
            request()->session()->flash('status', __("You don't have permissions to edit someone else's loot."));
            return redirect()->route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]);
        }

        $updateValues = [];

        $updateValues['public_note'] = request()->input('public_note');

        if ($currentMember->hasPermission('edit.officer-notes')) {
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

        if (!$guild->is_wishlist_disabled &&
            (!$guild->is_wishlist_locked
                || $currentMember->hasPermission('loot.characters')
                || ($currentMember->id == $character->member_id && $currentMember->is_wishlist_unlocked)
                || in_array(request()->input('wishlist_number'), $guild->getWishlistLockedExceptions())
            )
        ) {
            if (request()->input('wishlist')) {
                $maxWishlistItems = $guild->max_wishlist_items ? $guild->max_wishlist_items : self::MAX_WISHLIST_ITEMS;
                $this->syncItems(
                    $character->allWishlists,
                    array_slice(request()->input('wishlist'), 0, $maxWishlistItems),
                    Item::TYPE_WISHLIST,
                    $character,
                    $currentMember,
                    true,
                    false,
                    request()->input('wishlist_number')
                );
            }
        }

        if (!$guild->is_received_locked || $currentMember->hasPermission('loot.characters') || ($currentMember->id == $character->member_id && $currentMember->is_received_unlocked)) {
            if (request()->input('received')) {
                $markAsReceived = (request()->input('mark_as_received') == "1" ? true : false);
                // Don't bother enforcing an item limit here
                $this->syncItems(
                    $character->received,
                    request()->input('received'),
                    Item::TYPE_RECEIVED,
                    $character,
                    $currentMember,
                    true,
                    $markAsReceived,
                    1,
                );
            }
        }

        if (request()->input('recipes')) {
            // Don't bother enforcing an item limit here
            $this->syncItems(
                $character->recipes,
                request()->input('recipes'),
                Item::TYPE_RECIPE,
                $character,
                $currentMember,
                false,
                false,
                1,
            );
        }

        request()->session()->flash('status', __("Successfully updated loot."));

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
     * @param Collection    $existingItems  The items already attached to the character for this item type.
     * @param Array         $inputItems     The items provided from the HTML form input.
     * @param string        $itemType       The type of item. (ie. received, recipe, wishlist)
     * @param App\Character $character      The character to sync the items to.
     * @param App\Member    $currentMember  The member syncing these items.
     * @param boolean       $updateFlags    Should we check for and update the OS and received flag?
     * @param boolean       $markAsReceived Should we mark correlated prios/wishlists as received?
     * @param integer       $listNumber     Should this be applied to a specific list number for this item type?
     */
    private function syncItems($existingItems, $inputItems, $itemType, $character, $currentMember, $updateFlags, $markAsReceived, $listNumber) {
        $toAdd    = [];
        $toUpdate = [];
        $toDelete = [];

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

                    $inputItem['has_note'] = isset($inputItem['has_note']) ? 1 : 0;

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
                    // Received at date changed
                    if (isset($inputItem['new_received_at'])) {
                        $newValues['is_received'] = 1;
                        $newValues['received_at'] = Carbon::parse($inputItem['new_received_at'])->toDateTimeString();
                    }
                    // Raid changed
                    if (isset($inputItem['new_raid_id']) && $itemType == 'received') {
                        $newValues['raid_id'] = $inputItem['new_raid_id'];
                    }
                    // Is Offspec flag changed
                    if ($updateFlags && $existingItem->pivot->is_offspec != $inputItem['is_offspec']) {
                        if ($inputItem['is_offspec']) {
                            $newValues['is_offspec'] = 1;
                        } else {
                            $newValues['is_offspec'] = 0;
                        }
                    }
                    // Note changed
                    if (array_key_exists('note', $inputItem)) {
                        if ($existingItem->pivot->note != $inputItem['note']) {
                            $newValues['note'] = $inputItem['note'];
                        } else if (!$inputItem['has_note'] && $existingItem->pivot->note) {
                            $newValues['note'] = null;
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
                $toDelete[] = $existingItem->pivot->id;
                // Also remove it from the collection... for good measure I guess.
                $existingItems->forget($existingItemKey);

                $message = '';
                if (in_array($existingItem->pivot->type, [Item::TYPE_PRIO, Item::TYPE_WISHLIST])) {
                    $message = ' (rank ' . $existingItem->pivot->order . ')';
                }

                $audits[] = [
                    'description'  => $currentMember->username . ' removed item from a character (' . $itemType . ($itemType == 'wishlist' && $listNumber ? " {$listNumber}" : "") . ')' . $message,
                    'type'         => $itemType,
                    'member_id'    => $currentMember->id,
                    'raid_id'      => $existingItem->pivot->raid_id,
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
                $isReceived = isset($inputItem['is_received']) || isset($inputItem['new_received_at']) ? 1 : 0;
                $isOffspec  = isset($inputItem['is_offspec']) ? 1 : 0;
                $hasNote    = isset($inputItem['has_note']) ? 1 : 0;

                $receivedAt = null;
                if (isset($inputItem['new_received_at'])) {
                    $receivedAt = Carbon::parse($inputItem['new_received_at'])->toDateTimeString();
                } else if ($isReceived || $itemType == 'received') {
                    $receivedAt = Carbon::now()->toDateTimeString();
                }

                $raidId = null;
                if (isset($inputItem['new_raid_id']) && $itemType == 'received') {
                    $raidId = $inputItem['new_raid_id'];
                }

                $note = null;
                if ($hasNote && isset($inputItem['note'])) {
                    $note = $inputItem['note'];
                }

                $toAdd[] = [
                    'item_id'      => $inputItem['item_id'],
                    'is_received'  => $isReceived,
                    'is_offspec'   => $isOffspec,
                    'character_id' => $character->id,
                    'added_by'     => $currentMember->id,
                    'received_at'  => $receivedAt,
                    'list_number'  => $listNumber ? $listNumber : 1,
                    'raid_id'      => $raidId,
                    'type'         => $itemType,
                    'note'         => $note,
                    'order'        => $i,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];

                $audits[] = [
                    'description'  => "{$currentMember->username} added item to a character ({$itemType}" . ($itemType == 'wishlist' && $listNumber ? " {$listNumber}" : "") . ")"
                        . ($isReceived ? ' (received)' : null)
                        . ($isOffspec ? ' (OS)' : null)
                        . ($itemType == Item::TYPE_RECEIVED && $markAsReceived ? ' (prios and wishlists marked received)' : null),
                    'type'         => $itemType,
                    'member_id'    => $currentMember->id,
                    'raid_id'      => $raidId,
                    'guild_id'     => $currentMember->guild_id,
                    'character_id' => $character->id,
                    'item_id'      => $inputItem['item_id'],
                    'created_at'   => $now,
                ];
            }
        }

        // Delete...
        CharacterItem::whereIn('id', $toDelete)->delete();

        // Update...
        // I'm sure there's some clever way to perform an UPDATE statement with CASE statements... https://stackoverflow.com/questions/3432/multiple-updates-in-mysql
        // Don't have time for that just to remove a few queries.
        foreach ($toUpdate as $item) {
            $newValues = [];
            $auditMessage = '';

            // These keys only exist if we're changing them.
            if (isset($item['is_received']) && $itemType != 'received') {
                $newValues['is_received'] = $item['is_received'];
                if (!$item['is_received']) {
                    $newValues['received_at'] = null;
                }
                $auditMessage .= ($item['is_received'] ? 'set as received, ' : 'set as unreceived, ');
            }
            if (isset($item['received_at'])) {
                $newValues['received_at'] = $item['received_at'];
                $auditMessage .= 'set received date, ';
            }
            if (isset($item['is_offspec'])) {
                $newValues['is_offspec'] = $item['is_offspec'];
                $auditMessage .= ($item['is_offspec'] ? 'set as OS, ' : 'set as MS, ');
            }
            if (array_key_exists('note', $item)) {
                $newValues['note'] = $item['note'];
                $auditMessage .= ($item['note'] ? 'note added, ' : 'note removed, ');
            }
            if (isset($item['order'])) {
                $newValues['order'] = $item['order'];
                if ($auditMessage) {
                    $auditMessage .= 'order ' . $item['old_order'] . ' -> ' . $item['order'] . ', ';
                }
            }
            if (isset($item['raid_id']) && $itemType == 'received') {
                $newValues['raid_id'] = $item['raid_id'];
                $auditMessage .= 'changed raid, ';
            }
            $auditMessage = rtrim($auditMessage, ', ');

            $newValues['updated_at'] = $now;

            CharacterItem::where('id', $item['pivot_id'])->update($newValues);

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
                    'description'  => $currentMember->username . ' changed an item on ' . $character->name . ' (' . $itemType . ($itemType == 'wishlist' && $listNumber ? " {$listNumber}" : "") . '): ' . $auditMessage,
                    'type'         => $itemType,
                    'member_id'    => $currentMember->id,
                    'raid_id'      => isset($newValues['raid_id']) ? $newValues['raid_id'] : null,
                    'guild_id'     => $currentMember->guild_id,
                    'character_id' => $character->id,
                    'item_id'      => $item['item_id'],
                    'created_at'   => $now,
                ];
            }
        }

        if ($isReordered) {
            $audits[] = [
                'description'  => $currentMember->username . ' re-ordered items for a character (' . $itemType . ($itemType == 'wishlist' && $listNumber ? " {$listNumber}" : "") . ' items)',
                'type'         => $itemType,
                'member_id'    => $currentMember->id,
                'raid_id'      => null,
                'guild_id'     => $currentMember->guild_id,
                'character_id' => $character->id,
                'item_id'      => null,
                'created_at'   => $now,
            ];
        }

        // Insert...
        CharacterItem::insert($toAdd);

        // Find the first wishlist or prio item that matches what was just set as received,
        // and flag it as having been received.
        if ($itemType == Item::TYPE_RECEIVED && $markAsReceived) {
            $itemIds = array_map(function ($toAdd) {return $toAdd['item_id'];}, $toAdd);
            foreach ($itemIds as $addedItemId) {
                // Find first unreceived wishlist item and mark it as received
                $wishlistRows = CharacterItem::
                    select('character_items.*')
                    // Look for both the original item and the possible token reward for the item
                    ->join('items', function ($join) {
                        return $join->on('items.item_id', 'character_items.item_id')
                            ->orWhereRaw('`items`.`parent_item_id` = `character_items`.`item_id`');
                    })
                    ->where([
                        'character_items.character_id' => $character->id,
                        'character_items.type'         => Item::TYPE_WISHLIST,
                        'character_items.is_received'  => 0,
                    ])
                    ->whereRaw("(items.item_id = {$addedItemId} OR items.parent_item_id = {$addedItemId})")
                    ->groupBy(['character_items.list_number'])
                    ->orderBy('character_items.is_received')
                    ->orderBy('character_items.list_number')
                    ->orderBy('character_items.order')
                    ->get();

                if ($wishlistRows->count()) {
                    // Drop the first one from each wishlist
                    foreach($wishlistRows as $wishlistRow) {
                        CharacterItem::
                            where(['id' => $wishlistRow->id])
                            ->update([
                                'is_received' => 1,
                                'received_at' => $now,
                            ]);

                        $audits[] = [
                            'description'   => "System flagged 1 wishlist item (list {$wishlistRow->list_number}) as received after character was assigned item",
                            'type'          => Item::TYPE_WISHLIST,
                            'member_id'     => $currentMember->id,
                            'raid_id'       => $wishlistRow->raid_id,
                            'guild_id'      => $currentMember->guild_id,
                            'character_id'  => $character->id,
                            'item_id'       => $wishlistRow->item_id,
                            'created_at'    => $now,
                        ];
                    }
                }

                $prioRow = CharacterItem::where([
                        'item_id'      => $addedItemId,
                        'character_id' => $character->id,
                        'type'         => Item::TYPE_PRIO,
                        'is_received'  => 0,
                    ])
                    ->orderBy('is_received')
                    ->orderBy('order')
                    ->first();

                if ($prioRow) {
                    CharacterItem::
                        where(['id' => $prioRow->id])
                        ->update([
                            'is_received' => 1,
                            'received_at' => $now,

                        ]);

                    $audits[] = [
                        'description'   => 'System flagged 1 prio as received after character was assigned item',
                        'type'          => Item::TYPE_PRIO,
                        'member_id'     => $currentMember->id,
                        'raid_id'       => $prioRow->raid_id,
                        'guild_id'      => $currentMember->guild_id,
                        'character_id'  => $character->id,
                        'item_id'       => $prioRow->item_id,
                        'created_at'    => $now,
                    ];
                }
            }
        }

        AuditLog::insert($audits);
    }
}
