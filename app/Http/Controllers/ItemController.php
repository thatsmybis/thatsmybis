<?php

namespace App\Http\Controllers;

use App\{AuditLog, Guild, Instance, Item, Raid};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
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
     * List the items
     *
     * @return \Illuminate\Http\Response
     */
    public function listWithGuild($guildId, $guildSlug, $instanceSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raids']);

        $instance = Instance::where('slug', $instanceSlug)->firstOrFail();

        $characterFields = [
            'characters.id',
            'characters.raid_id',
            'characters.name',
            'characters.slug',
            'characters.level',
            'characters.race',
            'characters.spec',
            'characters.class',
            'characters.is_alt',
            'members.username',
            'raids.name AS raid_name',
            'raid_roles.color AS raid_color',
            'added_by_members.username AS added_by_username',
        ];

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $characterFields[] = 'characters.officer_note';
            $showOfficerNote = true;
        }

        $items = Item::select([
                'items.item_id',
                'items.name',
                'item_sources.name AS source_name',
                'guild_items.note AS guild_note',
                'guild_items.priority AS guild_priority',
            ])
            ->join('item_item_sources', function ($join) {
                $join->on('item_item_sources.item_id', 'items.item_id');
            })
            ->join('item_sources', function ($join) {
                $join->on('item_sources.id', 'item_item_sources.item_source_id');
            })
            ->leftJoin('guild_items', function ($join) use ($guild) {
                $join->on('guild_items.item_id', 'items.item_id')
                    ->where('guild_items.guild_id', $guild->id);
            })
            ->where('item_sources.instance_id', $instance->id)
            ->orderBy('item_sources.order')
            ->orderBy('items.name')
            ->with([

            ]);

        $showPrios = false;
        if (!$guild->is_prio_private || $currentMember->hasPermission('view.prios')) {
            $showPrios = true;
            $items = $items->with([
                'priodCharacters' => function ($query) use ($guild) {
                    return $query->where('characters.guild_id', $guild->id);
                }
            ]);
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_private || $currentMember->hasPermission('view.wishlists')) {
            $showWishlist = true;
            $items = $items->with([
                'wishlistCharacters' => function ($query) use($guild, $characterFields) {
                return $query->select($characterFields)
                    ->leftJoin('members', function ($join) {
                        $join->on('members.id', 'characters.member_id');
                    })
                    ->where([
                            ['characters.guild_id', $guild->id],
                            ['character_items.is_received', 0],
                        ])
                    ->groupBy(['character_items.character_id', 'character_items.item_id']);
                }
            ]);
        }

        $items = $items->get();

        return view('item.list', [
            'currentMember'   => $currentMember,
            'guild'           => $guild,
            'instance'        => $instance,
            'items'           => $items,
            'raids'           => $guild->raids,
            'showOfficerNote' => $showOfficerNote,
            'showPrios'       => $showPrios,
            'showWishlist'    => $showWishlist,
        ]);
    }

    /**
     * List the items
     *
     * @return \Illuminate\Http\Response
     */
    public function listWithGuildEdit($guildId, $guildSlug, $instanceSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.items')) {
            request()->session()->flash('status', 'You don\'t have permissions to view that page.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $instance = Instance::where('slug', $instanceSlug)
            ->with('itemSources')
            ->firstOrFail();

        $items = Item::select([
                'items.item_id',
                'items.name',
                'item_sources.name AS source_name',
                'guild_items.note AS guild_note',
                'guild_items.priority AS guild_priority',
            ])
            ->join('item_item_sources', function ($join) {
                $join->on('item_item_sources.item_id', 'items.item_id');
            })
            ->join('item_sources', function ($join) {
                $join->on('item_sources.id', 'item_item_sources.item_source_id');
            })
            ->leftJoin('guild_items', function ($join) use ($guild) {
                $join->on('guild_items.item_id', 'items.item_id')
                    ->where('guild_items.guild_id', $guild->id);
            })
            ->where('item_sources.instance_id', $instance->id)
            // Without this, we'd get the same item listed multiple times from multiple sources in some cases
            // This is problematic because the notes entered may differ, but we can only take one.
            ->groupBy('items.item_id')
            ->orderBy('item_sources.order')
            ->orderBy('items.name')
            ->get();

        return view('item.listEdit', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'instance'      => $instance,
            'items'         => $items,
        ]);
    }

    /**
     * List the items
     *
     * @return \Illuminate\Http\Response
     */
    public function listWithGuildSubmit($guildId, $guildSlug, $instanceSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.items')) {
            request()->session()->flash('status', 'You don\'t have permissions to submit that.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->Slug]);
        }

        $validationRules =  [
            'items.*.id'       => 'required|integer|exists:items,item_id',
            'items.*.note'     => 'nullable|string|max:144',
            'items.*.priority' => 'nullable|string|max:144',
        ];

        $this->validate(request(), $validationRules);

        $instance = Instance::where('slug', $instanceSlug)->firstOrFail();

        $guild->load([
            'items' => function ($query) use ($instance) {
                return $query
                    ->join('item_item_sources', function ($join) {
                        $join->on('item_item_sources.item_id', 'items.item_id');
                    })
                    ->join('item_sources', function ($join) {
                        $join->on('item_sources.id', 'item_item_sources.item_source_id');
                    })
                    ->groupBy('items.item_id')
                    ->where('item_sources.instance_id', $instance->id);
            }
        ]);

        $existingItems = $guild->items;
        $addedCount = 0;
        $updatedCount = 0;

        $audits = [];
        $now = getDateTime();

        // Perform updates and inserts. Note who performed the update. Don't update/insert unchanged/empty rows.
        foreach (request()->input('items') as $item) {
            $existingItem = $guild->items->where('item_id', $item['id'])->first();

            // Note or priority has changed; update it
            if ($existingItem && ($item['note'] != $existingItem->pivot->note || $item['priority'] != $existingItem->pivot->priority)) {
                $guild->items()->updateExistingPivot($existingItem->item_id, [
                    'note'       => $item['note'],
                    'priority'   => $item['priority'],
                    'updated_by' => $currentMember->id,
                ]);
                $updatedCount++;

                $audits[] = [
                    'description'  => $currentMember->username . ' changed item note/priority',
                    'member_id'    => $currentMember->id,
                    'guild_id'     => $currentMember->guild_id,
                    'item_id'      => $existingItem->item_id,
                    'created_at'   => $now,
                ];

            // Note is totally new; insert it
            } else if (!$existingItem && ($item['note'] || $item['priority'])) {
                $guild->items()->attach($item['id'], [
                    'note'       => $item['note'],
                    'priority'   => $item['priority'],
                    'created_by' => $currentMember->id,
                ]);
                $addedCount++;

                $audits[] = [
                    'description'  => $currentMember->username . ' added item note/priority',
                    'member_id'    => $currentMember->id,
                    'guild_id'     => $currentMember->guild_id,
                    'item_id'      => $item['id'],
                    'created_at'   => $now,
                ];
            }
        }

        AuditLog::insert($audits);

        request()->session()->flash('status', 'Successfully updated notes. ' . $addedCount . ' added, ' . $updatedCount . ' updated.');

        return redirect()->route('guild.item.list', [
            'guildId'      => $guild->id,
            'guildSlug'    => $guild->slug,
            'instanceSlug' => $instance->slug,
        ]);
    }

    /**
     * Show the mass input page
     *
     * @return \Illuminate\Http\Response
     */
    public function massInput($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'characters',
            'raids',
        ]);

        if (!$currentMember->hasPermission('edit.raid-loot')) {
            request()->session()->flash('status', 'You don\'t have permissions to view that page.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        return view('item.massInput', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
        ]);
    }

    /**
     * Show an item
     *
     * @return \Illuminate\Http\Response
     */
    public function showWithGuild($guildId, $guildSlug, $id, $slug = null)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raids']);

        $characterFields = [
            'characters.raid_id',
            'characters.name',
            'characters.level',
            'characters.race',
            'characters.spec',
            'characters.class',
            'members.username',
            'raids.name AS raid_name',
            'raid_roles.color AS raid_color',
        ];

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $item = Item::where('item_id', $id)->with([
            'guilds' => function ($query) use($guild) {
                return $query->select([
                    'guild_items.created_by',
                    'guild_items.updated_by',
                    'guild_items.note',
                    'guild_items.priority'
                ])
                ->where('guilds.id', $guild->id);
            },
            'receivedAndRecipeCharacters' => function ($query) use($guild) {
                return $query
                    ->where(['characters.guild_id' => $guild->id])
                    ->groupBy(['character_items.character_id']);
            },
        ]);

        $showPrios = false;
        if (!$guild->is_prio_private || $currentMember->hasPermission('view.prios')) {
            $item = $item->with([
                'priodCharacters' => function ($query) use ($guild) {
                    return $query
                        ->where(['characters.guild_id' => $guild->id]);
                },
            ]);
            $showPrios = true;
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_private || $currentMember->hasPermission('view.wishlists')) {
            $item = $item->with([
                'wishlistCharacters' => function ($query) use($guild) {
                    return $query
                        ->where([
                            ['characters.guild_id', $guild->id],
                            ['character_items.is_received', 0],
                        ])
                        ->groupBy(['character_items.character_id'])
                        ->with([
                            'prios',
                            'received',
                            'recipes',
                            'wishlist',
                        ]);
                },
            ]);
            $showWishlist = true;
        }

        $item = $item->firstOrFail();

        $itemSlug = slug($item->name);

        if ($slug && $slug != $itemSlug) {
            return redirect()->route('guild.item.show', [
                'guildId'   => $guild->id,
                'guildSlug' => $guild->slug,
                'item_id'   => $item->item_id,
                'slug'      => slug($item->name)
            ]);
        }

        $notes = [];
        $notes['note']     = null;
        $notes['priority'] = null;

        // If this guild has notes for this item, prep them for ease of access in the view
        if ($item->guilds->count() > 0) {
            $notes['note']     = $item->guilds->first()->pivot->note;
            $notes['priority'] = $item->guilds->first()->pivot->priority;
        }

        $showEdit = false;
        if ($currentMember->hasPermission('edit.characters')) {
            $showEdit = true;
        }

        $showNoteEdit = false;
        if ($currentMember->hasPermission('edit.items')) {
            $showNoteEdit = true;
        }

        $showPrioEdit = false;
        if ($currentMember->hasPermission('edit.prios')) {
            $showPrioEdit = true;
        }

        return view('item.show', [
            'currentMember'               => $currentMember,
            'guild'                       => $guild,
            'item'                        => $item,
            'notes'                       => $notes,
            'priodCharacters'             => $item->relationLoaded('priodCharacters') ? $item->priodCharacters : null,
            'raids'                       => $guild->raids,
            'receivedAndRecipeCharacters' => $item->receivedAndRecipeCharacters,
            'showEdit'                    => $showEdit,
            'showNoteEdit'                => $showNoteEdit,
            'showOfficerNote'             => $showOfficerNote,
            'showPrioEdit'                => $showPrioEdit,
            'showPrios'                   => $showPrios,
            'showWishlist'                => $showWishlist,
            'wishlistCharacters'          => $item->relationLoaded('wishlistCharacters') ? $item->wishlistCharacters : null,
            'itemJson'                    => self::getItemWowheadJson($item->item_id),
        ]);
    }

    public function submitMassInput($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        // Allow adding items to inactive characters as well
        // Perhaps someone deactivated a character while the raid leader was still editing the form
        // We don't want the submission to fail because of that
        $guild->load(['allCharacters']);

        $validationRules =  [
            'items.*.id'            => 'nullable|integer|exists:items,item_id',
            'items.*.character_id'  => 'nullable|integer|exists:characters,id',
            'delete_wishlist_items' => 'nullable|boolean',
            'delete_prio_items'     => 'nullable|boolean',
        ];

        $this->validate(request(), $validationRules);

        if (!$currentMember->hasPermission('edit.raid-loot')) {
            request()->session()->flash('status', 'You don\'t have permissions to submit that.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $deleteWishlist = request()->input('delete_wishlist_items') ? true : false;
        $deletePrio     = request()->input('delete_prio_items') ? true : false;

        $warnings   = '';
        $newRows    = [];
        $detachRows = [];
        $now        = getDateTime();

        $addedCount  = 0;
        $failedCount = 0;

        $audits = [];
        $now = getDateTime();

        foreach (request()->input('items') as $item) {
            if ($item['id']) {
                if ($guild->allCharacters->contains('id', $item['character_id'])) {
                    $newRows[] = [
                        'item_id'      => $item['id'],
                        'character_id' => $item['character_id'],
                        'added_by'     => $currentMember->id,
                        'type'         => Item::TYPE_RECEIVED,
                        'order'        => '0', // Put this item at the top of the list
                        'created_at'   => $now,
                    ];
                    $detachRows[] = [
                        'item_id'      => $item['id'],
                        'character_id' => $item['character_id'],
                    ];
                    $addedCount++;

                    $audits[] = [
                        'description'  => $currentMember->username . ' assigned item to character',
                        'member_id'    => $currentMember->id,
                        'character_id' => $item['character_id'],
                        'guild_id'     => $currentMember->guild_id,
                        'raid_id'      => null,
                        'item_id'      => $item['id'],
                        'created_at'   => $now,
                    ];
                } else {
                    $warnings .= (isset($item['label']) ? $item['label'] : $item['id']) . ' to character ID ' . $item['character_id'] . ', ';
                    $failedCount++;
                }
            }
        }

        // Add the items to the character's received list
        DB::table('character_items')->insert($newRows);

        // For each item added, attempt to delete or flag a matching item from the character's wishlist and prios
        foreach ($detachRows as $detachRow) {
            // Find wishlist for this item
            $wishlistRow = DB::table('character_items')->where([
                'item_id'      => $detachRow['item_id'],
                'character_id' => $detachRow['character_id'],
                'type'         => Item::TYPE_WISHLIST,
            ])->limit(1)->orderBy('order')->first();

            if ($wishlistRow) {
                if ($deleteWishlist) {
                    // Delete the one we found
                    DB::table('character_items')->where(['id' => $wishlistRow->id])->delete();
                    $audits[] = [
                        'description'  => 'System removed 1 wishlist item after character was assigned item',
                        'member_id'    => $currentMember->id,
                        'character_id' => $wishlistRow->character_id,
                        'guild_id'     => $currentMember->guild_id,
                        'raid_id'      => $wishlistRow->raid_id,
                        'item_id'      => $wishlistRow->item_id,
                        'created_at'   => $now,
                    ];
                } else {
                    DB::table('character_items')->where(['id' => $wishlistRow->id])
                        ->update([
                            'is_received' => 1,
                            'received_at' => getDateTime()
                        ]);

                    $audits[] = [
                        'description'  => 'System flagged 1 wishlist item as received after character was assigned item',
                        'member_id'    => $currentMember->id,
                        'character_id' => $wishlistRow->character_id,
                        'guild_id'     => $currentMember->guild_id,
                        'raid_id'      => $wishlistRow->raid_id,
                        'item_id'      => $wishlistRow->item_id,
                        'created_at'   => $now,
                    ];
                }
            }

            // Find prio for this item
            $prioRow = DB::table('character_items')->where([
                'item_id'      => $detachRow['item_id'],
                'character_id' => $detachRow['character_id'],
                'type'         => Item::TYPE_PRIO,
            ])->orderBy('order')->first();

            if ($prioRow) {
                $auditMessage = '';
                if ($deletePrio) {
                    // Delete the one we found
                    DB::table('character_items')->where(['id' => $prioRow->id])->delete();

                    // Now correct the order on the remaning prios for that item in that raid
                    DB::table('character_items')->where([
                            'item_id' => $prioRow->item_id,
                            'raid_id' => $prioRow->raid_id,
                            'type'    => Item::TYPE_PRIO,
                        ])
                        ->where('order', '>', $prioRow->order)
                        ->update(['order' => DB::raw('`order` - 1')]);
                    $auditMessage = 'removed 1 prio item';
                } else {
                    DB::table('character_items')->where(['id' => $prioRow->id])
                        ->update([
                            'is_received' => 1,
                            'received_at' => getDateTime()
                        ]);
                    $auditMessage = 'flagged 1 item prio as received';
                }

                $audits[] = [
                    'description'  => 'System ' . $auditMessage . ' after character was assigned item',
                    'member_id'    => $currentMember->id,
                    'character_id' => $prioRow->character_id,
                    'guild_id'     => $currentMember->guild_id,
                    'raid_id'      => $prioRow->raid_id,
                    'item_id'      => $prioRow->item_id,
                    'created_at'   => $now,
                ];
            }
        }

        AuditLog::insert($audits);

        request()->session()->flash('status', 'Successfully added ' . $addedCount . ' items. ' . $failedCount . ' failures' . ($warnings ? ': ' . rtrim($warnings, ', ') : '.'));

        return redirect()->route('member.show', [
            'guildId'      => $guild->id,
            'guildSlug'    => $guild->slug,
            'memberId'     => $currentMember->id,
            'usernameSlug' => $currentMember->slug
        ]);
    }

    /**
     * Update an item's notes
     * @return
     */
    public function updateNote($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raids', 'roles']);

        $validationRules = [
            'id'       => 'required|integer|exists:items,item_id',
            'note'     => 'nullable|string|max:144',
            'priority' => 'nullable|string|max:144',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $item = Item::findOrFail(request()->input('id'));

        if (!$currentMember->hasPermission('edit.items')) {
            request()->session()->flash('status', 'You don\'t have permissions to edit items.');
            return redirect()->route('guild.item.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'slug' => slug($item->name)]);
        }

        $existingRelationship = $guild->items()->find(request()->input('id'));

        $noticeVerb = null;

        if ($existingRelationship) {
            $noticeVerb = 'updated';

            $guild->items()->updateExistingPivot($item->item_id, [
                'note'       => request()->input('note'),
                'priority'   => request()->input('priority'),
                'updated_by' => $currentMember->id,
            ]);

            AuditLog::create([
                'description'  => $currentMember->username . ' changed item note/priority',
                'member_id'    => $currentMember->id,
                'guild_id'     => $currentMember->guild_id,
                'item_id'      => $item->item_id,
            ]);
        } else {
            $noticeVerb = 'created';

            $guild->items()->attach($item->item_id, [
                'note'       => request()->input('note'),
                'priority'   => request()->input('priority'),
                'created_by' => $currentMember->id,
            ]);

            AuditLog::create([
                'description'  => $currentMember->username . ' added item note/priority',
                'member_id'    => $currentMember->id,
                'guild_id'     => $currentMember->guild_id,
                'item_id'      => $item->item_id,
            ]);
        }

        request()->session()->flash('status', "Successfully " . $noticeVerb . " " . $item->name ."'s note.");

        return redirect()->route('guild.item.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'slug' => slug($item->name)]);
    }

    /**
     * Grab the JSON for an item from Wowhead, return only the HTML for the tooltip.
     *
     * @param int $id The ID of the item to fetch.
     */
    public static function getItemWowheadJson($id) {
        $json = null;
        try {
            // Suppressing warnings with the error control operator @ (if the id doesn't exist, it will fail to open stream)
            $json = json_decode(file_get_contents('https://classic.wowhead.com/tooltip/item/' . (int)$id));
        } catch (Exception $e) {
            // Fail silently, that's okay, we just won't display the content
        }
        return $json;
    }
}
