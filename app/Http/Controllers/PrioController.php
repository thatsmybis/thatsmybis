<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Guild, Instance, Item, Raid};
use Auth;
use Illuminate\Http\Request;

class PrioController extends Controller
{
    const MAX_PRIOS = 20;

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
    public function chooseRaid($guildSlug, $instanceSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        // if (!$currentMember->hasPermission('edit.raid-prios')) {
        //     request()->session()->flash('status', 'You don\'t have permissions to view that page.');
        //     return redirect()->route('member.show', ['guildSlug' => $guild->slug, 'username' => $currentMember->username]);
        // }

        $guild->load([
            'raids',
            'raids.role',
        ]);

        $instance = Instance::where('slug', $instanceSlug)->firstOrFail();

        return view('guild.prios.chooseRaid', [
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
    public function massInput($guildSlug, $instanceSlug, $raidId)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        // if (!$currentMember->hasPermission('edit.raid-prios')) {
        //     request()->session()->flash('status', 'You don\'t have permissions to view that page.');
        //     return redirect()->route('member.show', ['guildSlug' => $guild->slug, 'username' => $currentMember->username]);
        // }

        $guild->load([
            'characters',
        ]);

        $raid = Raid::where([
            'guild_id' => $guild->id,
            'id'       => $raidId,
        ])->firstOrFail();

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
            ->with([
                'receivedAndRecipeCharacters' => function ($query) use($guild) {
                    return $query
                        ->where([
                            'characters.guild_id' => $guild->id,
                        ])
                        ->groupBy(['character_items.character_id']);
                },
                'wishlistCharacters' => function ($query) use($guild) {
                    return $query
                        ->where([
                            'characters.guild_id' => $guild->id,
                        ])
                        ->groupBy(['character_items.character_id']);
                },
            ])
            ->get();

        return view('guild.prios.massInput', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raid'          => $raid,
            'instance'      => $instance,
            'items'         => $items,
            'maxPrios'      => self::MAX_PRIOS,
        ]);
    }

    public function submitMassInput($guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        // if (!$currentMember->hasPermission('edit.raid-prios')) {
        //     request()->session()->flash('status', 'You don\'t have permissions to view that page.');
        //     return redirect()->route('member.show', ['guildSlug' => $guild->slug, 'username' => $currentMember->username]);
        // }

        $validationRules =  [
            'instance_id'           => 'required|exists:instances,id',
            'items.*.id'            => 'nullable|integer|exists:items,item_id',
            'items.*.characters.id' => 'nullable|integer|exists:characters,id',
            'raid_id'               => 'required|exists:raids,id',
        ];

        $this->validate(request(), $validationRules);

        $raid = Raid::where(['guild_id' => $guild->id, 'id' => request()->input('raid_id')])->firstOrFail();

        $instance = Instance::findOrFail(request()->input('instance_id'));

        dd(request()->input());

        // get existing prios for that raid

        // iterate over items
            // are there input prios for this item?
            // yes
                // were there db prios?
                // yes
                    // syncPrios()
                        // copy from charactercontroller
                        // toAdd, toUpdate, toDrop
                        // audit log
                // no
                    // add the new ones
                    // audit log that we added them
            // no
                // were there db prios?
                // yes
                    // remove them
                    // audit log that we removed them
                // no
                    // do nothing
    }

    private function syncPrios() {
        // todo
    }
}
