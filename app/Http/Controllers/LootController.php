<?php

namespace App\Http\Controllers;

use App\{Character, Guild, Instance, Item};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
                    'items.faction',
                    'items.is_heroic',
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
            'charactersWithAttendance' => null,
            'currentMember'   => null,
            'expansionId'     => $expansionId,
            'guild'           => null,
            'instance'        => $instance,
            'items'           => $items,
            'raidGroups'      => null,
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
    public function showWishlistStats($expansionName = null, $class = null) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $expansionId = null;

        if ($expansionName == 'classic' || $expansionName === 1) {
            $expansionId = 1;
        } else if ($expansionName == 'tbc' || $expansionName === 2) {
            $expansionId = 2;
        } else if ($expansionName == 'wotlk' || $expansionName === 3) {
            $expansionId = 3;
        } else if ($expansionName == 'sod' || $expansionName === 4) {
            $expansionId = 4;
        } else if ($expansionName == 'cata' || $expansionName === 5) {
            $expansionId = 5;
        } else if ($expansionName == 'mop' || $expansionName === 6) {
            $expansionId = 6;
        } else {
            $expansionId = 1;
        }

        $classes = Character::classes($expansionId);

        if (!$guild && !$expansionName) {
            return redirect()->route('loot.wishlist', ['expansionName' => 'classic', 'class' => $class]);
        }

        if (!$class) {
            $class = array_keys($classes)[0];
        } else {
            $class = ucwords(strtolower(str_replace('-', ' ', $class)));
        }

        $validationRules = [
            ['class' => ['string', Rule::in(array_keys($classes))]],
        ];

        $this->validate(request(), $validationRules, []);

        $archetypes = Character::archetypes();

        $specsWithItems = Cache::remember(
            "wishlist_stats:expansion_id:{$expansionId}:class:{$class}",
            env('PUBLIC_WISHLIST_CACHE_SECONDS', 259200),
            function () use ($expansionId, $class, $archetypes) {
                $items = self::getWishlistStats($expansionId, $class);

                // Get specs as objects
                $specs = collect(Character::specs($expansionId))
                    ->map(function($spec){ return (object)$spec; });

                foreach ($specs as $specKey => &$spec) {
                    $spec->items = $items->where('spec', $specKey)->where('class', $spec->class);

                    $spec->archetypes = collect();
                    foreach ($archetypes as $archetypeKey => $archetype) {
                        $spec->archetypes->put(
                            strtolower($archetypeKey),
                            $items->where('spec', $specKey)->where('archetype', $archetypeKey)->sum('wishlist_count')
                        );
                    }
                }

                return $specs;
            }
        );

        return view('loot.wishlistStats', [
            'archetypes'      => $archetypes,
            'chosenClass'     => $class,
            'classes'         => $classes,
            'currentMember'   => $currentMember,
            'expansionId'     => $expansionId,
            'guild'           => $guild,
            'maxItems'        => self::MAX_LIST_ITEMS,
            'specsWithItems'  => $specsWithItems,
        ]);
    }

    public function showWishlistStatsInGuild($guildId, $guildSlug, $class = null) {
        $guild = request()->get('guild');

        return $this->showWishlistStats($guild->expansion_id, $class);
    }


    public static function getWishlistStats($expansionId, $class) {
        $cutoffDate = null;
        // Manual exceptions for Phases
        if ($expansionId === 4) {
            // 3 months or this date, whichever is LESS time
            $cutoffDate1 = "2024-08-01 00:00:00";
            $cutoffDate2 = date('Y-m-d H:i:s', strtotime("-3 months"));
            if ($cutoffDate1 > $cutoffDate2) {
                $cutoffDate = $cutoffDate1;
            } else {
                $cutoffDate = $cutoffDate2;
            }
        } else {
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-6 months"));
        }
        return collect(DB::select(
            DB::raw(
                "SELECT
                    -- Step 3: Only fetch the rows we desire.
                    `class`,
                    `spec`,
                    `archetype`,
                    `name`,
                    `quality`,
                    {$expansionId} AS 'expansion_id',
                    `faction`,
                    `item_id`,
                    `wishlist_count`,
                    `instance_name`,
                    `instance_short_name`
                FROM
                (
                    -- Step 2: Add row numbers to the data, separated by each class, spec, and archetype.
                    SELECT
                        `class`,
                        `spec`,
                        `archetype`,
                        `name`,
                        `quality`,
                        `faction`,
                        `item_id`,
                        `wishlist_count`,
                        `instance_name`,
                        `instance_short_name`,
                        (@rowNumber:=if(@prev = CONCAT(`class`, `spec`, `archetype`), @rowNumber +1, 1)) as rowNumber,
                        @prev:= CONCAT(`class`, `spec`, `archetype`)
                        FROM
                        (
                            -- Step 1: Fetch all of the data.
                            SELECT
                                c.`class`,
                                IFNULL(c.`spec`, '') AS 'spec',
                                IFNULL(c.`archetype`, '') AS 'archetype',
                                i.`name` AS 'name',
                                i.`quality` AS `quality`,
                                i.`faction` AS `faction`,
                                i.`item_id` AS 'item_id',
                                COUNT(ci.`id`) as 'wishlist_count',
                                `instances`.`name` AS 'instance_name',
                                `instances`.`short_name` AS 'instance_short_name'
                            FROM `items` i
                            JOIN `character_items` ci ON ci.`item_id` = i.`item_id`
                            JOIN `characters` c ON c.`id` = ci.`character_id`
                            JOIN `guilds` g ON g.`id` = c.`guild_id`
                            LEFT JOIN `item_item_sources` iis ON iis.`item_id` = i.`item_id`
                            LEFT JOIN `item_sources` isource ON isource.`id` = iis.`item_source_id`
                            LEFT JOIN `instances` ON `instances`.`id` = isource.`instance_id`
                            WHERE
                                g.`expansion_id` = :expansionId
                                AND ci.`type` = :listType
                                AND c.`class` = :class
                                AND ci.`created_at` > '{$cutoffDate}'
                                AND i.`item_id` != 3857 -- Someone keeps spamming Coal on wishlists.
                                -- To fetch ALL classes, change to:
                                -- AND c.`class` IS NOT NULL
                            GROUP BY i.`item_id`, c.`spec`, c.`class`, c.`archetype`
                            ORDER BY c.`class` ASC, `spec` ASC, `archetype` ASC, `wishlist_count` DESC, `name`
                        ) AS wishlistData
                        JOIN (SELECT @prev:=NULL, @rowNumber :=0) as variables
                ) AS groupedWishlists
                WHERE rowNumber <= :maxRows"
            ),
            [
                'class'       => $class,
                'listType'    => Item::TYPE_WISHLIST,
                'expansionId' => $expansionId,
                'maxRows'     => self::MAX_LIST_ITEMS
            ]
        ));
    }
}
