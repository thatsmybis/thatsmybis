<?php

namespace App\Http\Controllers;

use App\{Instance, Item};
use Illuminate\Support\Facades\Cache;

class LootController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([]);
    }

    /**
     * Show the loot page.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('loot.show');
    }

    /**
     * List the items
     *
     * @return \Illuminate\Http\Response
     */
    public function list($expansionId, $instanceSlug)
    {
        $instance = Cache::remember('instance:' . $instanceSlug, 600, function () use ($instanceSlug) {
            return Instance::where('slug', $instanceSlug)->firstOrFail();
        });

        $items = Cache::remember('instance:' . $instanceSlug . ':loot:expansion:' . $expansionId, 600, function () use ($instance, $expansionId) {
            return Item::select([
                    'items.item_id',
                    'items.name',
                    'items.quality',
                    'item_sources.name AS source_name',
                ])
                ->join('item_item_sources', function ($join) {
                    $join->on('item_item_sources.item_id', 'items.item_id');
                })
                ->join('item_sources', function ($join) {
                    $join->on('item_sources.id', 'item_item_sources.item_source_id');
                })
                ->where([
                    ['item_sources.instance_id', $instance->id],
                    ['items.expansion_id', $expansionId],
                ])
                ->orderBy('item_sources.order')
                ->orderBy('items.name')
                ->get();
        });

        return view('item.list', [
            'currentMember'   => null,
            'guild'           => null,
            'instance'        => $instance,
            'items'           => $items,
            'raids'           => null,
            'showNotes'       => true,
            'showOfficerNote' => true,
            'showPrios'       => true,
            'showWishlist'    => true,
        ]);
    }
}
