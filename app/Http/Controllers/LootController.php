<?php

namespace App\Http\Controllers;

use App\{Character, Instance, Item};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LootController extends Controller
{
    const MAX_LIST_ITEMS = 25;

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
        $instance = Cache::remember('instance:' . $instanceSlug, env('PUBLIC_EXPORT_CACHE_SECONDS', 600), function () use ($instanceSlug) {
            return Instance::where('slug', $instanceSlug)->firstOrFail();
        });

        $items = Cache::remember('instance:' . $instanceSlug . ':loot:expansion:' . $expansionId, env('PUBLIC_EXPORT_CACHE_SECONDS', 600), function () use ($instance, $expansionId) {
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
            'expansionId'     => $expansionId,
            'guild'           => null,
            'instance'        => $instance,
            'items'           => $items,
            'raids'           => null,
            'showNotes'       => false,
            'showOfficerNote' => false,
            'showPrios'       => true,
            'showWishlist'    => true,
        ]);
    }

    /**
     * Show the top X wishlisted items for each class.
     *
     * @return \Illuminate\Http\Response
     */
    public function showWishlistStats($expansionId = null) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        if ($expansionId == 'classic') {
            $expansionId = 1;
        } else if ($expansionId == 'tbc') {
            $expansionId = 2;
        } else if (!$expansionId) {
            $expansionId = 2;
        }

        $wishlists = array_fill_keys(Character::classes($expansionId), null);

        foreach ($wishlists as $key => $value) {
            $wishlists[$key] = self::getWishlistStats($key, $expansionId);
        }

        return view('loot.wishlistStats', [
            'wishlists'       => $wishlists,
            'currentMember'   => $currentMember,
            'expansionId'     => $expansionId,
            'guild'           => $guild,
            'maxItems'        => self::MAX_LIST_ITEMS,
        ]);
    }

    public function showWishlistStatsInGuild($guildId, $guildSlug) {
        $guild = request()->get('guild');
        return $this->showWishlistStats($guild->expansion_id);
    }


    public static function getWishlistStats($class, $expansionId) {
        $validClasses = Character::classes($expansionId);

        if (in_array(strtolower($class), array_map('strtolower', $validClasses))) {
            $items = Cache::remember("wishlist_picks:class:{$class}:expansion_id:{$expansionId}", env('PUBLIC_EXPORT_CACHE_SECONDS', 600), function () use ($expansionId, $class) {
                return Item::select([
                        'items.id',
                        'items.item_id',
                        'items.name',
                        'items.expansion_id',
                        'instances.short_name as instance_name',
                        DB::raw("'{$class}' AS `class`"),
                        DB::raw("count(distinct character_items.id) AS `count`")
                    ])
                    ->join('item_item_sources', function ($join) {
                        $join->on('item_item_sources.item_id', 'items.item_id');
                    })
                    ->join('item_sources', function ($join) {
                        $join->on('item_sources.id', 'item_item_sources.item_source_id');
                    })
                    ->join('instances', function ($join) {
                        $join->on('instances.id', 'item_sources.instance_id');
                    })
                    ->join('character_items', function ($join) {
                        $join->on('character_items.item_id', 'items.item_id');
                    })
                    ->join('characters', function ($join) {
                        $join->on('characters.id', 'character_items.character_id');
                    })
                    ->join('guilds', function ($join) {
                        $join->on('guilds.id', 'characters.guild_id');
                    })
                    ->where([
                        ['character_items.type', Item::TYPE_WISHLIST],
                        ['characters.class', $class],
                        ['guilds.expansion_id', $expansionId],
                        ['items.expansion_id', $expansionId],
                    ])
                    ->groupBy('items.item_id')
                    ->orderBy('count', 'desc')
                    ->limit(self::MAX_LIST_ITEMS)
                    ->get();
            });
        } else {
            $items = collect();
        }

        return $items;
    }
}
