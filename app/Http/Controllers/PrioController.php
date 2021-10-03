<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, CharacterItem, Guild, Instance, Item, RaidGroup};
use App\Http\Controllers\ItemController;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator};
use Illuminate\Validation\Rule;

class PrioController extends Controller
{
    const MAX_PRIOS = 40;

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
     * Show the mass input page
     *
     * @return \Illuminate\Http\Response
     */
    public function chooseRaidGroup($guildId, $guildSlug, $instanceSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($guild->is_prio_disabled || !$currentMember->hasPermission('edit.prios')) {
            if ($guild->is_prio_disabled) {
                request()->session()->flash('status', __('Prios are disabled by guild leadership.'));
            } else {
                request()->session()->flash('status', __("You don't have permissions to view that page."));
            }
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load([
            'raidGroups',
            'raidGroups.role',
        ]);

        $instance = Instance::where('slug', $instanceSlug)->firstOrFail();

        return view('guild.prios.chooseRaidGroup', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'instance'      => $instance,
        ]);
    }

    /**
     * Show the mass input page
     *
     * @return \Illuminate\Http\Response
     */
    public function assignPrios($guildId, $guildSlug, $instanceSlug, $raidGroupId)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($guild->is_prio_disabled || !$currentMember->hasPermission('edit.prios')) {
            if ($guild->is_prio_disabled) {
                request()->session()->flash('status', __('Prios are disabled by guild leadership.'));
            } else {
                request()->session()->flash('status', __("You don't have permissions to view that page."));
            }
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load([
            'characters',
        ]);

        $raidGroup = RaidGroup::where([
            'guild_id' => $guild->id,
            'id'       => $raidGroupId,
        ])->firstOrFail();

        $instance = Instance::where('slug', $instanceSlug)
            ->with('itemSources')
            ->firstOrFail();

        $query = Item::select([
                'items.id',
                'items.item_id',
                'items.name',
                'items.quality',
                'item_sources.name      AS source_name',
                'guild_items.note       AS guild_note',
                'guild_items.priority   AS guild_priority',
                'guild_items.tier       AS guild_tier',
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
            ->where([
                ['item_sources.instance_id', $instance->id],
                ['items.expansion_id', $guild->expansion_id],
            ])
            ->whereNull('items.parent_id')
            // Without this, we'd get the same item listed multiple times from multiple sources in some cases
            // This is problematic because the notes entered may differ, but we can only take one.
            ->groupBy('items.item_id')
            ->orderBy('item_sources.order')
            ->orderBy('items.name')
            ->with([
                'priodCharacters' => function ($query) use ($raidGroup) {
                    return $query
                        ->where('character_items.raid_group_id', $raidGroup->id)
                        ->groupBy(['character_items.character_id', 'character_items.item_id']);
                },
                'receivedAndRecipeCharacters' => function ($query) use($guild, $raidGroup) {
                    return $query
                        ->leftJoin('character_raid_groups', function ($join) {
                            $join->on('character_raid_groups.character_id', 'characters.id');
                        })
                        ->where([
                            'characters.guild_id' => $guild->id,
                        ])
                        ->whereRaw("(characters.raid_group_id = {$raidGroup->id} OR character_raid_groups.raid_group_id = {$raidGroup->id})")
                        ->groupBy(['character_items.character_id', 'character_items.item_id']);
                },
            ]);

        if ($guild->is_wishlist_disabled) {
            $query = $query->with([
                'childItems',
            ]);
        } else {
            $query = $query->with([
                'wishlistCharacters' => function ($query) use($guild, $raidGroup) {
                    return $query
                        ->leftJoin('character_raid_groups', function ($join) {
                            $join->on('character_raid_groups.character_id', 'characters.id');
                        })
                        ->where([
                            'characters.guild_id' => $guild->id,
                            'is_received'         => 0,
                            'list_number'         => DB::raw('`wishlist_guilds`.`current_wishlist_number`'),
                        ])
                        ->whereRaw("(`characters`.`raid_group_id` = {$raidGroup->id} OR `character_raid_groups`.`raid_group_id` = {$raidGroup->id})")
                        ->groupBy(['character_items.character_id', 'character_items.item_id', 'character_items.list_number'])
                        ->orderBy('character_items.order');
                },
                'childItems' => function ($query) use ($guild, $raidGroup) {
                    return $query->with([
                        'wishlistCharacters' => function ($query) use($guild, $raidGroup) {
                            return $query
                                ->leftJoin('character_raid_groups', function ($join) {
                                    $join->on('character_raid_groups.character_id', 'characters.id');
                                })
                                ->where([
                                    'characters.guild_id' => $guild->id,
                                    'is_received'         => 0,
                                    'list_number'         => DB::raw('`wishlist_guilds`.`current_wishlist_number`'),
                                ])
                                ->whereRaw("(`characters`.`raid_group_id` = {$raidGroup->id} OR `character_raid_groups`.`raid_group_id` = {$raidGroup->id})")
                                ->whereNull('characters.inactive_at')
                                ->groupBy(['character_items.character_id', 'character_items.item_id', 'character_items.list_number'])
                                ->orderBy('character_items.order');
                        },
                    ]);
                },
            ]);
        }

        $items = $query->get();

        if (!$guild->is_wishlist_disabled) {
            $items = ItemController::mergeTokenWishlists($items, $guild);

            // For optimization, fetch characters with their attendance here and then merge them into
            // the existing characters for prios and wishlists
            if (!$guild->is_attendance_hidden) {
                $charactersWithAttendance = Guild::getAllCharactersWithAttendanceCached($guild);
                foreach ($items as $item) {
                    $item->wishlistCharacters = Character::mergeAttendance($item->wishlistCharacters, $charactersWithAttendance);
                }
            }
        }

        return view('guild.prios.assignPrios', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raidGroup'     => $raidGroup,
            'instance'      => $instance,
            'items'         => $items,
            'maxPrios'      => self::MAX_PRIOS,
        ]);
    }

    /**
     * Show an item's character priorities for a specific raid group for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function singleInput($guildId, $guildSlug, $itemId, $raidGroupId) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($guild->is_prio_disabled || !$currentMember->hasPermission('edit.prios')) {
            if ($guild->is_prio_disabled) {
                request()->session()->flash('status', __('Prios are disabled by guild leadership.'));
            } else {
                request()->session()->flash('status', __("You don't have permissions to view that page."));
            }
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $guild->load('characters');

        $raidGroup = RaidGroup::where(['guild_id' => $guild->id, 'id' => $raidGroupId])->firstOrFail();

        $items = Item::select([
                'items.id',
                'items.item_id',
                'items.name',
                'items.quality',
                'item_sources.name      AS source_name',
                'guild_items.note       AS guild_note',
                'guild_items.priority   AS guild_priority',
                'guild_items.tier       AS guild_tier',
            ])
            ->leftJoin('item_item_sources', function ($join) {
                $join->on('item_item_sources.item_id', 'items.item_id');
            })
            ->leftJoin('item_sources', function ($join) {
                $join->on('item_sources.id', 'item_item_sources.item_source_id');
            })
            ->leftJoin('guild_items', function ($join) use ($guild) {
                $join->on('guild_items.item_id', 'items.item_id')
                    ->where('guild_items.guild_id', $guild->id);
            })
            ->where([
                ['items.item_id', $itemId],
                ['items.expansion_id', $guild->expansion_id],
            ])
            ->whereNull('items.parent_id')
            ->groupBy('items.item_id')
            ->with([
                'priodCharacters' => function ($query) use ($raidGroup) {
                    return $query
                        ->where('character_items.raid_group_id', $raidGroup->id)
                        ->groupBy(['character_items.character_id']);
                },
                'receivedAndRecipeCharacters' => function ($query) use($guild, $raidGroup) {
                    return $query
                        ->leftJoin('character_raid_groups', function ($join) {
                            $join->on('character_raid_groups.character_id', 'characters.id');
                        })
                        ->where([
                            'characters.guild_id' => $guild->id,
                        ])
                        ->whereRaw("(characters.raid_group_id = {$raidGroup->id} OR character_raid_groups.raid_group_id = {$raidGroup->id})")
                        ->groupBy(['character_items.character_id', 'character_items.item_id']);
                },
                'wishlistCharacters' => function ($query) use($guild, $raidGroup) {
                    return $query
                        ->leftJoin('character_raid_groups', function ($join) {
                            $join->on('character_raid_groups.character_id', 'characters.id');
                        })
                        ->where([
                            'characters.guild_id'      => $guild->id,
                            'is_received'              => 0,
                        ])
                        ->whereRaw("(characters.raid_group_id = {$raidGroup->id} OR character_raid_groups.raid_group_id = {$raidGroup->id})")
                        ->groupBy(['character_items.character_id', 'character_items.item_id']);
                },
                'childItems' => function ($query) use ($guild, $raidGroup) {
                    return $query->with([
                        'wishlistCharacters' => function ($query) use($guild, $raidGroup) {
                            return $query
                                ->leftJoin('character_raid_groups', function ($join) {
                                    $join->on('character_raid_groups.character_id', 'characters.id');
                                })
                                ->where([
                                    'characters.guild_id'      => $guild->id,
                                    'is_received'              => 0,
                                ])
                                ->whereRaw("(characters.raid_group_id = {$raidGroup->id} OR character_raid_groups.raid_group_id = {$raidGroup->id})")
                                ->whereNull('characters.inactive_at')
                                ->groupBy(['character_items.character_id', 'character_items.item_id']);
                        },
                    ]);
                },
            ])
            ->get();

        if (!$guild->is_wishlist_disabled) {
            $items = ItemController::mergeTokenWishlists($items, $guild);
        }

        $item = $items->first();

        if (!$item) {
            request()->session()->flash('status', __("Item not found. Can't set prios on items that don't drop from a boss or aren't in our loot tables, including token rewards."));
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $wishlistCharacters = null;

        if (!$guild->is_wishlist_disabled) {
            $wishlistCharacters = $item->wishlistCharacters;

            // For optimization, fetch characters with their attendance here and then merge them into
            // the existing characters for prios and wishlists
            if (!$guild->is_attendance_hidden) {
                $charactersWithAttendance = Guild::getAllCharactersWithAttendanceCached($guild);
                $wishlistCharacters = Character::mergeAttendance($wishlistCharacters, $charactersWithAttendance);
            }
        }

        return view('guild.prios.singleInput', [
            'currentMember'      => $currentMember,
            'guild'              => $guild,
            'item'               => $item,
            'maxPrios'           => self::MAX_PRIOS,
            'raidGroup'          => $raidGroup,
            'wishlistCharacters' => $wishlistCharacters,
        ]);
    }


    /**
     * Submit the mass input page for prios, for a given dungeon and raid group.
     */
    public function submitAssignPrios($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($guild->is_prio_disabled || !$currentMember->hasPermission('edit.prios')) {
            if ($guild->is_prio_disabled) {
                request()->session()->flash('status', __('Prios are disabled by guild leadership.'));
            } else {
                request()->session()->flash('status', __("You don't have permissions to view that page."));
            }
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules =  [
            'instance_id'   => 'required|exists:instances,id',
            'raid_group_id' => 'required|exists:raid_groups,id',
            'items.*.item_id' => [
                'nullable',
                'integer',
                Rule::exists('items', 'item_id')->where('items.expansion_id', $guild->expansion_id),
            ],
            'items.*.characters.*.character_id' => [
                'nullable',
                'integer',
                Rule::exists('characters', 'id')->where('characters.guild_id', $guild->id),
            ],
            'items.*.characters.*.character_id' => 'nullable|integer|exists:characters,id',
            'items.*.characters.*.is_received'  => 'nullable|boolean',
            'items.*.characters.*.is_offspec'   => 'nullable|boolean',
            'items.*.characters.*.order'        => 'nullable|integer|min:1|max:' . self::MAX_PRIOS,
        ];

        $this->validate(request(), $validationRules);

        $guild->load('characters');

        $raidGroup = RaidGroup::where(['guild_id' => $guild->id, 'id' => request()->input('raid_group_id')])->firstOrFail();

        $instance = Instance::findOrFail(request()->input('instance_id'));

        // Get items for the specified instance WITH any existing prios
        $itemsWithExistingPrios = Item::select(['items.*'])
            ->join('item_item_sources', function ($join) {
                $join->on('item_item_sources.item_id', 'items.item_id');
            })
            ->join('item_sources', function ($join) {
                $join->on('item_sources.id', 'item_item_sources.item_source_id');
            })
            ->where([
                ['items.expansion_id',       $guild->expansion_id],
                ['item_sources.instance_id', $instance->id],
            ])
            ->with([
                'priodCharacters' => function ($query) use ($raidGroup) {
                    return $query
                        ->where('character_items.raid_group_id', $raidGroup->id)
                        ->groupBy(['character_items.character_id', 'character_items.item_id']);
                },
            ])
            ->groupBy('items.item_id')
            ->get();

        $modifiedCount = $this->syncPrios($itemsWithExistingPrios, request()->input('items'), $currentMember, $guild->characters, $raidGroup);

        request()->session()->flash('status', __('Successfully updated prios for :count items in :raidGroup.', ['count' => $modifiedCount, 'raidGroup' => $raidGroup->name]));

        return redirect()->route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug, 'b' => 1]);
    }

    /**
     * Submit priorities for an item
     *
     * @return \Illuminate\Http\Response
     */
    public function submitSingleInput($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($guild->is_prio_disabled || !$currentMember->hasPermission('edit.prios')) {
            if ($guild->is_prio_disabled) {
                request()->session()->flash('status', __('Prios are disabled by guild leadership.'));
            } else {
                request()->session()->flash('status', __("You don't have permissions to view that page."));
            }
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $validationRules =  [
            'raid_group_id' => 'required|exists:raid_groups,id',
            'item_id' => [
                'required',
                'integer',
                Rule::exists('items', 'item_id')->where('items.expansion_id', $guild->expansion_id),
            ],
            'items.*.characters.*.character_id' => [
                'nullable',
                'integer',
                Rule::exists('characters', 'id')->where('characters.guild_id', $guild->id),
            ],
            'items.*.characters.*.character_id' => 'nullable|integer|exists:characters,id',
            'items.*.characters.*.is_received'  => 'nullable|boolean',
            'items.*.characters.*.is_offspec'   => 'nullable|boolean',
            'items.*.characters.*.order'        => 'nullable|integer|min:1|max:' . self::MAX_PRIOS,
        ];

        $this->validate(request(), $validationRules);

        $guild->load('characters');

        $raidGroup = RaidGroup::where(['guild_id' => $guild->id, 'id' => request()->input('raid_group_id')])->firstOrFail();

        $itemsWithExistingPrios = Item::
            where([
                ['items.expansion_id', $guild->expansion_id],
                ['items.item_id',      request()->input('item_id')],
            ])
            ->with([
                'priodCharacters' => function ($query) use ($raidGroup) {
                    return $query
                        ->where('character_items.raid_group_id', $raidGroup->id)
                        ->groupBy(['character_items.character_id', 'character_items.item_id']);
                },
            ])
            ->get();

        $modifiedCount = $this->syncPrios($itemsWithExistingPrios, request()->input('items'), $currentMember, $guild->characters, $raidGroup);

        if ($modifiedCount) {
            request()->session()->flash('status', __('Successfully updated prios for :itemName in :raidGroup.', ['itemName' => $itemsWithExistingPrios->first()->name, 'raidGroup' => $raidGroup->name]));
        } else {
            request()->session()->flash('status', __('No changes made to prios for :itemName in :raidGroup.', ['itemName' => $itemsWithExistingPrios->first()->name, 'raidGroup' => $raidGroup->name]));
        }

        return redirect()->route('guild.item.show', [
            'guildId'   => $guild->id,
            'guildSlug' => $guild->slug,
            'item_id'   => $itemsWithExistingPrios->first()->item_id,
            'slug'      => slug($itemsWithExistingPrios->first()->name),
            'b'         => 1,
        ]);
    }

    /**
     * A custom sync function that allows for duplicate entries. I didn't see a clear way
     * to allow duplicates using Laravel's provided sync functions for collections. RIP.
     *
     * Heavy on the comments because my brain was having a hard time.
     *
     * @param Collection     $itemsWithExistingPrios A collection of items with their current prios
     * @param Array          $inputItems    The items provided from the HTML form input.
     * @param App\Member     $currentMember The member syncing these items.
     * @param App\Characters $characters    The characters we're allowed to attach to.
     * @param App\RaidGroup  $raidGroup     The raid group to associate these prios with.
     *
     * @return int The number of items that had their prios modified.
     */
    private static function syncPrios($itemsWithExistingPrios, $inputItems, $currentMember, $characters, $raidGroup) {
        $characters = $characters->keyBy('id');

        $toAdd    = [];
        $toUpdate = [];
        $toDrop   = [];

        $modifiedCount = 0;

        $audits = [];
        $now = getDateTime();

        // We only iterate over items that we were able to fetch from the database for this instance.
        // The means if a user attempts to add new items to the page, they will be ignored.
        // We're already making potentially thousands of iterations (items*prios), this helps ensure this limit.
        foreach ($itemsWithExistingPrios as $existingItem) {
            $inputItem = $inputItems[$existingItem->item_id];

            if ($inputItem) {
                // Filter out empty inputs
                if (isset($inputItem['characters'])) {
                    $inputPrios = array_filter($inputItem['characters'], function ($value) {return $value['character_id'];});
                } else {
                    $inputPrios = [];
                }

                $existingPrios = $existingItem->priodCharacters;

                $toUpdateCount = 0;

                $isModified = false;

                /**
                 * Go over all the prios we already have in the database.
                 * If any of these are found in the set sent from the input, we're going to update them with new metadata.
                 * If any of these aren't found in the input, they shouldn't exist anymore so we'll drop them.
                 */
                foreach ($existingPrios as $existingPrioKey => $existingPrio) {
                    $found = false;
                    $i = 0;
                    foreach ($inputPrios as $inputPrioKey => $inputPrio) {
                        if (isset($characters[$inputPrio['character_id']])) {
                            $i++;
                            // We found a match
                            if (!isset($inputPrios[$inputPrioKey]['resolved']) && $existingPrio->id == $inputPrio['character_id']) {
                                $found = true;
                                $changed = false;
                                $newValues = [];

                                $order      = isset($inputPrio['order']) ? $inputPrio['order'] : $i;
                                $isReceived = isset($inputPrio['is_received']) && $inputPrio['is_received'] ? 1 : 0;
                                $isOffspec  = isset($inputPrio['is_offspec']) && $inputPrio['is_offspec'] ? 1 : 0;

                                if ($existingPrio->pivot->order != $order) {
                                    // Update the metadata
                                    $changed = true;
                                    $newValues['id']    = $existingPrio->pivot->id;
                                    $newValues['order'] = $order;
                                }

                                if ($existingPrio->pivot->is_received != $isReceived) {
                                    $changed = true;
                                    $newValues['id']          = $existingPrio->pivot->id;
                                    $newValues['is_received'] = $isReceived;
                                    if ($isReceived) {
                                        $newValues['received_at'] = $now;
                                    } else {
                                        $newValues['received_at'] = null;
                                    }
                                }

                                if ($existingPrio->pivot->is_offspec != $isOffspec) {
                                    $changed = true;
                                    $newValues['id']         = $existingPrio->pivot->id;
                                    $newValues['is_offspec'] = $isOffspec;
                                }

                                if ($changed) {
                                    // Since we are using UPSERT, these fields MUST be present. Populate them if they are missing.
                                    if (!array_key_exists('received_at', $newValues)) {
                                        $newValues['received_at'] = $existingPrio->pivot->received_at;
                                    }
                                    if (!array_key_exists('order', $newValues)) {
                                        $newValues['order'] = $existingPrio->pivot->order;
                                    }
                                    if (!array_key_exists('is_received', $newValues)) {
                                        $newValues['is_received'] = $existingPrio->pivot->is_received;
                                    }
                                    if (!array_key_exists('is_offspec', $newValues)) {
                                        $newValues['is_offspec'] = $existingPrio->pivot->is_offspec;
                                    }

                                    $toUpdate[] = $newValues;
                                    $toUpdateCount++;
                                }

                                // Mark the input item as resolved so that we don't go over it again (we've already resolved what to do with this item)
                                $inputPrios[$inputPrioKey]['resolved'] = true;
                                break;
                            }
                        } else {
                            // Member isn't in allowed list of members; get rid of it.
                            unset($inputPrios[$inputPrioKey]);
                        }
                    }

                    // We didn't find this item in the input, so we should get rid of it
                    if (!$found) {
                        // We'll drop them all at once later on, rather than executing individual queries
                        $toDrop[] = $existingPrio->pivot->id;
                        // Also remove it from the collection... for good measure I guess.
                        $existingPrios->forget($existingPrioKey);

                        $isModified = true;
                        $audits[] = [
                            'description'   => $currentMember->username . ' removed a prio from a character (rank ' . $existingPrio->pivot->order . ')',
                            'type'          => Item::TYPE_PRIO,
                            'member_id'     => $currentMember->id,
                            'guild_id'      => $currentMember->guild_id,
                            'character_id'  => $existingPrio->id,
                            'item_id'       => $existingItem->item_id,
                            'raid_group_id' => $raidGroup->id,
                            'created_at'    => $now,
                        ];
                    }
                }

                /**
                 * Now we're left with just the prios from the form that didn't already exist in the database.
                 * We're going to add these to the database.
                 */
                $i = 0;
                foreach ($inputPrios as $inputPrio) {
                    $i++;

                    $order = isset($inputPrio['order']) && $inputPrio['order'] ? $inputPrio['order'] : $i;

                    if (!isset($inputPrio['resolved'])) {
                        $toAdd[] = [
                            'item_id'       => $inputItem['item_id'],
                            'character_id'  => $inputPrio['character_id'],
                            'is_offspec'    => isset($inputPrio['is_offspec']) && $inputPrio['is_offspec'] ? 1 : 0,
                            'is_received'   => isset($inputPrio['is_received']) && $inputPrio['is_received'] ? 1 : 0,
                            'received_at'   => isset($inputPrio['is_received']) && $inputPrio['is_received'] ? $now : null,
                            'added_by'      => $currentMember->id,
                            'raid_group_id' => $raidGroup->id,
                            'type'          => Item::TYPE_PRIO,
                            'order'         => $order,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ];

                        $isModified = true;
                        $audits[] = [
                            'description'   => $currentMember->username . ' prio\'d an item to a character (' . $order . ')',
                            'type'          => Item::TYPE_PRIO,
                            'member_id'     => $currentMember->id,
                            'guild_id'      => $currentMember->guild_id,
                            'character_id'  => $inputPrio['character_id'],
                            'item_id'       => $inputItem['item_id'],
                            'raid_group_id' => $raidGroup->id,
                            'created_at'    => $now,
                        ];
                    }
                }

                if ($toUpdateCount > 0) {
                    $isModified = true;
                    $audits[] = [
                        'description'   => $currentMember->username . ' altered ' . $toUpdateCount . ' prios for an item',
                        'type'          => Item::TYPE_PRIO,
                        'member_id'     => $currentMember->id,
                        'guild_id'      => $currentMember->guild_id,
                        'character_id'  => null,
                        'item_id'       => $inputItem['item_id'],
                        'raid_group_id' => $raidGroup->id,
                        'created_at'    => $now,
                    ];
                }

                if ($isModified) {
                    $modifiedCount++;
                }
            }
        }

        // Delete...
        CharacterItem::whereIn('id', $toDrop)->delete();

        // Update...
        CharacterItem::
            upsert(
                $toUpdate, // New data, includes `id` column
                ['id'], // Identifying column
                [ // Fields to be updated
                    'order',
                    'is_offspec',
                    'is_received',
                    'received_at',
                ]
            );

        // Insert...
        CharacterItem::insert($toAdd);

        AuditLog::insert($audits);

        return $modifiedCount;
    }
}
