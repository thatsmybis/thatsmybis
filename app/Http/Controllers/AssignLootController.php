<?php

namespace App\Http\Controllers;

use App\{AuditLog, Batch, Item};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AssignLootController extends Controller
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

    // Maximum number of items that can be added at any one time
    const MAX_ITEMS = 150;

    /**
     * Show the mass input page
     *
     * @return \Illuminate\Http\Response
     */
    public function assignLoot($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load([
            'characters',
            'raidGroups',
            'raids' => function ($query) {
                return $query->limit(100);
            },
        ]);

        if (!$currentMember->hasPermission('edit.raid-loot')) {
            request()->session()->flash('status', 'You don\'t have permissions to view that page.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        return view('item.assignLoot', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'maxItems'      => self::MAX_ITEMS,
        ]);
    }

    // Submit a whole bunch of loot at once
    public function submitAssignLoot($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $validationRules = [
            'raid_group_id' => [
                'nullable',
                'integer',
                Rule::exists('raid_groups', 'id')->where('raid_groups.guild_id', $guild->id),
            ],
            'raid_id'       => [
                'nullable',
                'integer',
                Rule::exists('raids', 'id')->where('raids.guild_id', $guild->id),
            ],
            'items.*.id' => [
                'nullable',
                'integer',
                Rule::exists('items', 'item_id')->where('items.expansion_id', $guild->expansion_id),
            ],
            'item.*.character_id'   => [
                'nullable',
                'integer',
                'exists:characters,id',
            ],
            'item.*.is_offspec'     => 'nullable|boolean',
            'item.*.note'           => 'nullable|string|max:140',
            'item.*.officer_note'   => 'nullable|string|max:140',
            'item.*.received_at'    => 'nullable|date|before:tomorrow|after:2004-09-22',
            // Would be nice to find a way to get it to check against `character_items`.`character_id` != item.*.character_id
            // Check the history of this file for some possible ideas on how to do it.
            // For now we'll just check the ID and call it good enough.
            'item.*.import_id'        => 'nullable|string|max:20|unique:character_items,import_id',
            'delete_wishlist_items'   => 'nullable|boolean',
            'delete_prio_items'       => 'nullable|boolean',
            'skip_missing_characters' => 'nullable|boolean',
        ];

        // We're not skipping characters, so add the rule that character_id must be set.
        if (!request()->input('skip_missing_characters')) {
            $validationRules['item.*.character_id'][] = 'required_with:item.*.id';
        }

        $validationMessages = [
            'item.*.character_id.required_with' => ':values is missing a character.',
            'item.*.import_id.unique'           => '":input" has been imported before. If you want to import anyway, change/remove the ID and submit again.',
        ];

        $this->validate(request(), $validationRules, $validationMessages);

        if (!$currentMember->hasPermission('edit.raid-loot')) {
            request()->session()->flash('status', 'You don\'t have permissions to submit that.');
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

        $raidGroupInputId = request()->input('raid_group_id');
        $raidInputId      = request()->input('raid_id');

        $raidGroup = null;
        if ($raidGroupInputId) {
            $guild->load([
                'raidGroups' => function ($query) use ($raidGroupInputId) {
                    return $query->where('id', $raidGroupInputId);
                }
            ]);
            $raidGroup = $guild->raidGroups->first();
        }

        $raid = null;
        if ($raidInputId) {
            $guild->load([
                'raids' => function ($query) use ($raidInputId) {
                    return $query->where('id', $raidInputId);
                }
            ]);
            $raid = $guild->raids->first();
        }

        $guild->load([
            // Allow adding items to inactive characters as well
            // Perhaps someone deactivated a character while the raid leader was still editing the form
            // We don't want the submission to fail because of that
            'allCharacters',
        ]);



        $deleteWishlist = request()->input('delete_wishlist_items') ? true : false;
        $deletePrio     = request()->input('delete_prio_items') ? true : false;
        $raidGroupId    = $raidGroup ? $raidGroup->id : null;
        $raidId         = $raid ? $raid->id : null;

        $warnings   = '';
        $newRows    = [];
        $detachRows = [];
        $now        = getDateTime();

        $addedCount  = 0;
        $failedCount = 0;

        $audits = [];
        $now = getDateTime();

        foreach (request()->input('item') as $item) {
            if ($item['id']) {
                if ($item['character_id']) {
                    if ($guild->allCharacters->contains('id', $item['character_id'])) {
                        $newRows[] = [
                            'item_id'       => $item['id'],
                            'character_id'  => $item['character_id'],
                            'added_by'      => $currentMember->id,
                            'raid_group_id' => $raidGroupId,
                            'raid_id'       => $raidId,
                            'type'          => Item::TYPE_RECEIVED,
                            'order'         => '0', // Put this item at the top of the list
                            'is_offspec'    => (isset($item['is_offspec']) && $item['is_offspec'] == true ? 1 : 0),
                            'is_received'   => 1,
                            'note'          => ($item['note']         ? $item['note'] : null),
                            'officer_note'  => ($item['officer_note'] ? $item['officer_note'] : null),
                            'received_at'   => ($item['received_at']  ? Carbon::parse($item['received_at'])->toDateTimeString() : getDateTime()),
                            'import_id'     => ($item['import_id']    ? $item['import_id'] : null),
                            'created_at'    => $now,
                        ];
                        $detachRows[] = [
                            'item_id'      => $item['id'],
                            'character_id' => $item['character_id'],
                        ];
                        $addedCount++;

                        $description = $currentMember->username . ' assigned item to character';

                        if (isset($item['is_offspec']) && $item['is_offspec'] == 1) {
                            $description .= ' (OS)';
                        }

                        if ($item['received_at']) {
                            $description .= ' (backdated ' . $item['received_at'] . ')';
                        }

                        $audits[] = [
                            'description'   => $description,
                            'type'          => AuditLog::TYPE_ASSIGN,
                            'member_id'     => $currentMember->id,
                            'character_id'  => $item['character_id'],
                            'guild_id'      => $currentMember->guild_id,
                            'raid_group_id' => $raidGroupId,
                            'raid_id'       => $raidId,
                            'item_id'       => $item['id'],
                            'created_at'    => $now,
                        ];
                    } else {
                        $warnings .= (isset($item['label']) ? $item['label'] : $item['id']) . ' to character ID ' . $item['character_id'] . ', ';
                        $failedCount++;
                    }
                } else {
                    $warnings .= (isset($item['label']) ? $item['label'] : $item['id']) . ' to missing character, ';
                    $failedCount++;
                }
            }
        }

        // Create a batch record for this job
        // Doing this right before we do the inserts just in case something went wrong beforehand
        $batch = Batch::create([
            'name'          => request()->input('name') ? request()->input('name') : null,
            'note'          => $currentMember->username . ' assigned ' . count($newRows) . ' items' . ($raidGroup ? ' on raid group ' . $raidGroup->name : '')  . ($raid ? ' on raid ' . $raid->name : ''),
            'type'          => AuditLog::TYPE_ASSIGN,
            'guild_id'      => $guild->id,
            'member_id'     => $currentMember->id,
            'raid_group_id' => $raidGroupId,
            'raid_id'       => $raidId,
            'user_id'       => $currentMember->user_id,
        ]);

        // Add the batch ID to the items we're going to insert
        array_walk($newRows, function (&$value, $key) use ($batch) {
            $value['batch_id'] = $batch->id;
        });

        // Add the items to the character's received list
        DB::table('character_items')->insert($newRows);

        // For each item added, attempt to delete or flag a matching item from the character's wishlist and prios
        foreach ($detachRows as $detachRow) {
            $whereClause = [
                'character_items.character_id' => $detachRow['character_id'],
                'character_items.type'         => Item::TYPE_WISHLIST,
            ];

            if (!$deleteWishlist) {
                $whereClause['character_items.is_received'] = 0;
            }

            // Find wishlist for this item
            $wishlistRow = DB::table('character_items')
                ->select('character_items.*')
                // Look for both the original item and the possible token reward for the item
                ->join('items', function ($join) {
                    return $join->on('items.item_id', 'character_items.item_id')
                        ->orWhereRaw('`items`.`parent_item_id` = `character_items`.`item_id`');
                })
                ->where($whereClause)
                ->whereRaw("(items.item_id = {$detachRow['item_id']} OR items.parent_item_id = {$detachRow['item_id']})")
                ->limit(1)
                ->orderBy('character_items.is_received')
                ->orderBy('character_items.order')
                ->first();

            if ($wishlistRow) {
                if ($deleteWishlist) {
                    // Delete the one we found
                    DB::table('character_items')->where(['id' => $wishlistRow->id])->delete();
                    $audits[] = [
                        'description'   => 'System removed 1 wishlist item after character was assigned item',
                        'type'          => Item::TYPE_WISHLIST,
                        'member_id'     => $currentMember->id,
                        'character_id'  => $wishlistRow->character_id,
                        'guild_id'      => $currentMember->guild_id,
                        'raid_group_id' => $wishlistRow->raid_group_id,
                        'item_id'       => $wishlistRow->item_id,
                        'created_at'    => $now,
                    ];
                } else {
                    DB::table('character_items')->where(['id' => $wishlistRow->id])
                        ->update([
                            'is_received' => 1,
                            'received_at' => $now,
                        ]);

                    $audits[] = [
                        'description'   => 'System flagged 1 wishlist item as received after character was assigned item',
                        'type'          => Item::TYPE_WISHLIST,
                        'member_id'     => $currentMember->id,
                        'character_id'  => $wishlistRow->character_id,
                        'guild_id'      => $currentMember->guild_id,
                        'raid_group_id' => $wishlistRow->raid_group_id,
                        'item_id'       => $wishlistRow->item_id,
                        'created_at'    => $now,
                    ];
                }
            }

            $whereClause = [
                'item_id'      => $detachRow['item_id'],
                'character_id' => $detachRow['character_id'],
                'type'         => Item::TYPE_PRIO,
            ];

            if (!$deletePrio) {
                $whereClause['is_received'] = 0;
            }

            // Find prio for this item
            $prioRow = DB::table('character_items')->where($whereClause)->orderBy('is_received')->orderBy('order')->first();

            if ($prioRow) {
                $auditMessage = '';
                if ($deletePrio) {
                    // Delete the one we found
                    DB::table('character_items')->where(['id' => $prioRow->id])->delete();

                    // Now correct the order on the remaning prios for that item in that raid group
                    DB::table('character_items')->where([
                            'item_id'       => $prioRow->item_id,
                            'raid_group_id' => $prioRow->raid_group_id,
                            'type'          => Item::TYPE_PRIO,
                        ])
                        ->where('order', '>', $prioRow->order)
                        ->update(['order' => DB::raw('`order` - 1')]);
                    $auditMessage = 'removed 1 prio';
                } else {
                    DB::table('character_items')->where(['id' => $prioRow->id])
                        ->update([
                            'is_received' => 1,
                            'received_at' => $now,

                        ]);
                    $auditMessage = 'flagged 1 prio as received';
                }

                $audits[] = [
                    'description'   => 'System ' . $auditMessage . ' after character was assigned item',
                    'type'          => Item::TYPE_PRIO,
                    'member_id'     => $currentMember->id,
                    'character_id'  => $prioRow->character_id,
                    'guild_id'      => $currentMember->guild_id,
                    'raid_group_id' => $prioRow->raid_group_id,
                    'item_id'       => $prioRow->item_id,
                    'created_at'    => $now,
                ];
            }
        }

        // Add the batch ID to the audit log records
        array_walk($audits, function (&$value, $key) use ($batch) {
            $value['batch_id'] = $batch->id;
        });

        AuditLog::insert($audits);

        request()->session()->flash('status', 'Successfully added ' . $addedCount . ' items. ' . $failedCount . ' failures' . ($warnings ? ': ' . rtrim($warnings, ', ') : '.'));

        return redirect()->route('guild.auditLog', [
            'guildId'   => $guild->id,
            'guildSlug' => $guild->slug,
            'batch_id'  => $batch->id,
        ]);
    }
}
