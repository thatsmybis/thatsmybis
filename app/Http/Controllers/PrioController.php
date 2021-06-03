<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Guild, Instance, Item, RaidGroup};
use App\Http\Controllers\ItemController;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PrioController extends Controller
{
    const MAX_PRIOS = 15;

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
                request()->session()->flash('status', 'Prios are disabled by the man.');
            } else {
                request()->session()->flash('status', 'You don\'t have permissions to view that page.');
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
    public function massInput($guildId, $guildSlug, $instanceSlug, $raidGroupId)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($guild->is_prio_disabled || !$currentMember->hasPermission('edit.prios')) {
            if ($guild->is_prio_disabled) {
                request()->session()->flash('status', 'Prios are disabled by the man.');
            } else {
                request()->session()->flash('status', 'You don\'t have permissions to view that page.');
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
                        ->where([
                            'characters.guild_id'      => $guild->id,
                            'characters.raid_group_id' => $raidGroup->id,
                        ]);
                },
            ]);

        if ($guild->is_wishlist_disabled) {
            $query = $query->with([
                'childItems',
            ]);
        } else {
            $query = $query->with([
                ($guild->is_attendance_hidden ? 'wishlistCharacters' : 'wishlistCharactersWithAttendance') => function ($query) use($guild, $raidGroup) {
                    return $query
                        ->where([
                            'characters.guild_id'      => $guild->id,
                            'characters.raid_group_id' => $raidGroup->id,
                            'is_received'              => 0,
                        ])
                        ->groupBy(['character_items.character_id', 'character_items.item_id']);
                },
                'childItems' => function ($query) use ($guild, $raidGroup) {
                    return $query->with([
                        ($guild->is_attendance_hidden ? 'wishlistCharacters' : 'wishlistCharactersWithAttendance') => function ($query) use($guild, $raidGroup) {
                            return $query
                                ->where([
                                    'characters.guild_id'      => $guild->id,
                                    'characters.raid_group_id' => $raidGroup->id,
                                    'is_received'              => 0,
                                ])
                            ->groupBy(['character_items.character_id', 'character_items.item_id']);
                        },
                    ]);
                },
            ]);
        }

        $items = $query->get();

        $items = ItemController::mergeTokenWishlists($items, $guild);

        return view('guild.prios.massInput', [
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
                request()->session()->flash('status', 'Prios are disabled by the man.');
            } else {
                request()->session()->flash('status', 'You don\'t have permissions to view that page.');
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
                        ->where([
                            'characters.guild_id'      => $guild->id,
                            'characters.raid_group_id' => $raidGroup->id,
                        ])
                        ->groupBy(['character_items.character_id']);
                },
                ($guild->is_attendance_hidden ? 'wishlistCharacters' : 'wishlistCharactersWithAttendance') => function ($query) use($guild, $raidGroup) {
                    return $query
                        ->where([
                            'characters.guild_id'      => $guild->id,
                            'characters.raid_group_id' => $raidGroup->id,
                            'is_received'              => 0,
                        ])
                        ->groupBy(['character_items.character_id']);
                },
                'childItems' => function ($query) use ($guild, $raidGroup) {
                    return $query->with([
                        ($guild->is_attendance_hidden ? 'wishlistCharacters' : 'wishlistCharactersWithAttendance') => function ($query) use($guild, $raidGroup) {
                            return $query
                                ->where([
                                    'characters.guild_id'      => $guild->id,
                                    'characters.raid_group_id' => $raidGroup->id,
                                    'is_received'              => 0,
                                ])
                            ->groupBy(['character_items.character_id', 'character_items.item_id']);
                        },
                    ]);
                },
            ])
            ->get();

        $items = ItemController::mergeTokenWishlists($items, $guild);

        $item = $items->first();

        $wishlistCharacters = null;
        if ($guild->is_attendance_hidden && $item->relationLoaded('wishlistCharacters')) {
            $wishlistCharacters = $item->wishlistCharacters;
        } else if ($item->relationLoaded('wishlistCharactersWithAttendance')) {
            $wishlistCharacters = $item->wishlistCharactersWithAttendance;
        }

        return view('item.prioEdit', [
            'currentMember'      => $currentMember,
            'guild'              => $guild,
            'item'               => $item,
            'maxPrios'           => self::MAX_PRIOS,
            'raidGroup'          => $raidGroup,
            'wishlistCharacters' => $wishlistCharacters,
        ]);
    }

    public function submitMassInput($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($guild->is_prio_disabled || !$currentMember->hasPermission('edit.prios')) {
            if ($guild->is_prio_disabled) {
                request()->session()->flash('status', 'Prios are disabled by the man.');
            } else {
                request()->session()->flash('status', 'You don\'t have permissions to view that page.');
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
            'items.*.characters.*.order'        => 'nullable|integer|min:0|max:' . self::MAX_PRIOS,
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

        request()->session()->flash('status', 'Successfully updated prios for ' . $modifiedCount . ' items in ' . $raidGroup->name . '.');
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
                request()->session()->flash('status', 'Prios are disabled by the man.');
            } else {
                request()->session()->flash('status', 'You don\'t have permissions to view that page.');
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
            'items.*.characters.*.order'        => 'nullable|integer|min:0|max:' . self::MAX_PRIOS,
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

        request()->session()->flash('status', ($modifiedCount ? 'Successfully updated prios for ' : 'No changes made to prios for ') . $itemsWithExistingPrios->first()->name . ' in ' . $raidGroup->name . '.');
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
                $inputPrios = array_filter($inputItem['characters'], function ($value) {return $value['character_id'];});

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
                                // TODO: Update the logic for the custom order, is_received, and is_offspec
                                if ($existingPrio->pivot->order != $i) {
                                    // Update the metadata
                                    $toUpdate[] = [
                                        'id'         => $existingPrio->pivot->id,
                                        'order'      => $i,
                                    ];
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

                    if (!isset($inputPrio['resolved'])) {
                        $toAdd[] = [
                            'item_id'       => $inputItem['item_id'],
                            'character_id'  => $inputPrio['character_id'],
                            'added_by'      => $currentMember->id,
                            'raid_group_id' => $raidGroup->id,
                            'type'          => Item::TYPE_PRIO,
                            'order'         => $i,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ];

                        $isModified = true;
                        $audits[] = [
                            'description'   => $currentMember->username . ' prio\'d an item to a character (' . $i . ')',
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
        DB::table('character_items')->whereIn('id', $toDrop)->delete();

        // Update...
        // I'm sure there's some clever way to perform an UPDATE statement with CASE statements... https://stackoverflow.com/questions/3432/multiple-updates-in-mysql
        // Don't have time to implement that.
        foreach ($toUpdate as $item) {
            DB::table('character_items')
                ->where('id', $item['id'])
                ->update([
                    'order'      => $item['order'],
                    'updated_at' => $now,
                ]);

            // If we want to log EVERY prio change (this has a cascading effect and can result in thousands of audits)
            // $audits[] = [
            //     'description'  => $currentMember->username . ' updated prio order on a character (prio set to ' . $item['order'] . ')',
            //     'member_id'    => $currentMember->id,
            //     'guild_id'     => $currentMember->guild_id,
            //     'item_id'      => $item['id'],
            // ];
        }

        // Insert...
        DB::table('character_items')->insert($toAdd);

        AuditLog::insert($audits);

        return $modifiedCount;
    }
}
