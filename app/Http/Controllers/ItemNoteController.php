<?php

namespace App\Http\Controllers;

use App\{AuditLog, Guild, Instance, Item};
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItemNoteController extends Controller
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
     * List the items for editing tier, note, and prio note
     *
     * @return \Illuminate\Http\Response
     */
    public function listWithGuildEdit($guildId, $guildSlug, $instanceSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if (!$currentMember->hasPermission('edit.items')) {
            request()->session()->flash('status', __("You don't have permissions to view that page."));
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->slug]);
        }

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
            // ->whereNull('items.parent_id')
            // Without this, we'd get the same item listed multiple times from multiple sources in some cases
            // This is problematic because the notes entered may differ, but we can only take one.
            ->groupBy('items.item_id')
            ->orderBy('item_sources.order')
            ->orderBy('items.name')
            ->ofFaction($guild->faction)
            ->with('childItems', function ($query) use ($guild) {
                $query->ofFaction($guild->faction);
            });

        $items = $query->get();

        $averageTiers = $this->getItemAverageTiers($instance, $guild->expansion_id);

        return view('item.listEdit', [
            'averageTiers'  => $averageTiers,
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
            request()->session()->flash('status', __("You don't have permissions to submit that."));
            return redirect()->route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $currentMember->id, 'usernameSlug' => $currentMember->Slug]);
        }

        $validationRules =  [
            'items.*.id' => [
                'required',
                'integer',
                Rule::exists('items', 'item_id')->where('items.expansion_id', $guild->expansion_id),
            ],
            'items.*.note'     => 'nullable|string|max:140',
            'items.*.priority' => 'nullable|string|max:140',
            'items.*.tier'     => ['nullable', 'integer', Rule::in(array_keys(Guild::tiers()))],
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
            if ($existingItem && (
                    $item['note'] != $existingItem->pivot->note
                    || $item['priority'] != $existingItem->pivot->priority
                    || $item['tier'] != $existingItem->pivot->tier
                )
            ) {
                $guild->items()->updateExistingPivot($existingItem->item_id, [
                    'note'       => $item['note'],
                    'priority'   => $item['priority'],
                    'updated_by' => $currentMember->id,
                    'tier'       => $item['tier'],
                ]);
                $updatedCount++;

                $audits[] = [
                    'description'  => $currentMember->username . ' changed item note/priority/tier',
                    'type'         => AuditLog::TYPE_ITEM_NOTE,
                    'member_id'    => $currentMember->id,
                    'guild_id'     => $currentMember->guild_id,
                    'item_id'      => $existingItem->item_id,
                    'created_at'   => $now,
                ];

            // Note is totally new; insert it
            } else if (!$existingItem && ($item['note'] || $item['priority'] || $item['tier'])) {
                $guild->items()->attach($item['id'], [
                    'note'       => $item['note'],
                    'priority'   => $item['priority'],
                    'created_by' => $currentMember->id,
                    'tier'       => $item['tier'],
                ]);
                $addedCount++;

                $audits[] = [
                    'description'  => $currentMember->username . ' added item note/priority/tier',
                    'type'         => AuditLog::TYPE_ITEM_NOTE,
                    'member_id'    => $currentMember->id,
                    'guild_id'     => $currentMember->guild_id,
                    'item_id'      => $item['id'],
                    'created_at'   => $now,
                ];
            }
        }

        AuditLog::insert($audits);

        request()->session()->flash('status', __('Successfully updated notes. :addedCount added, :updatedCount updated.', ['addedCount' => $addedCount, 'updatedCount' => $updatedCount]));

        return redirect()->route('guild.item.list', [
            'guildId'      => $guild->id,
            'guildSlug'    => $guild->slug,
            'instanceSlug' => $instance->slug,
        ]);
    }

    /**
     * Update an item's notes
     * @return
     */
    public function updateNote($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raidGroups', 'roles']);

        $validationRules = [
            'id' => [
                'required',
                'integer',
                Rule::exists('items', 'item_id')->where('items.expansion_id', $guild->expansion_id),
            ],
            'note'     => 'nullable|string|max:140',
            'priority' => 'nullable|string|max:140',
            'tier'     => ['nullable', 'integer', Rule::in(array_keys(Guild::tiers()))],
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $item = Item::findOrFail(request()->input('id'));

        if (!$currentMember->hasPermission('edit.items')) {
            request()->session()->flash('status', __("You don't have permissions to edit items."));
            return redirect()->route('guild.item.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'slug' => slug($item->name)]);
        }

        $existingRelationship = $guild->items()->find(request()->input('id'));

        $message = null;

        if ($existingRelationship) {
            $message = __("Successfully updated :itemName's note.", ['itemName' => $item->name]);

            $guild->items()->updateExistingPivot($item->item_id, [
                'note'       => request()->input('note'),
                'priority'   => request()->input('priority'),
                'tier'       => request()->input('tier'),
                'updated_by' => $currentMember->id,
            ]);

            AuditLog::create([
                'description' => $currentMember->username . ' changed item note/priority/tier',
                'member_id'   => $currentMember->id,
                'guild_id'    => $currentMember->guild_id,
                'item_id'     => $item->item_id,
            ]);
        } else {
            $message = __("Successfully created :itemName's note.", ['itemName' => $item->name]);

            $guild->items()->attach($item->item_id, [
                'note'       => request()->input('note'),
                'priority'   => request()->input('priority'),
                'tier'       => request()->input('tier'),
                'created_by' => $currentMember->id,
            ]);

            AuditLog::create([
                'description' => $currentMember->username . ' added item note/priority/tier',
                'member_id'   => $currentMember->id,
                'guild_id'    => $currentMember->guild_id,
                'item_id'     => $item->item_id,
            ]);
        }

        request()->session()->flash('status', $message);

        return redirect()->route('guild.item.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'slug' => slug($item->name), 'b' => 1]);
    }

    public static function getItemAverageTiers($instance, $expansionId) {
        return Cache::remember('tiers:instance:' . $instance->id . ':expansion:' . $expansionId, env('PUBLIC_EXPORT_CACHE_SECONDS', 600), function () use ($instance, $expansionId) {
            return Item::select([
                'items.item_id',
                DB::raw('AVG(`guild_items`.`tier`) AS `average_tier`'),
            ])
            ->join('item_item_sources', function ($join) {
                $join->on('item_item_sources.item_id', 'items.item_id');
            })
            ->join('item_sources', function ($join) {
                $join->on('item_sources.id', 'item_item_sources.item_source_id');
            })
            ->leftJoin('guild_items', function ($join) {
                $join->on('guild_items.item_id', 'items.item_id');
            })
            ->where([
                ['item_sources.instance_id', $instance->id],
                ['items.expansion_id', $expansionId],
            ])
            ->groupBy('items.item_id')
            ->orderBy('item_sources.order')
            ->orderBy('items.name')
            ->get()
            ->keyBy('item_id');
        });
    }
}
