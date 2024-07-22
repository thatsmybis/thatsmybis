<?php

namespace App\Http\Controllers;

use App\{Item};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RecipeController extends Controller
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
    public function listRecipesWithGuild($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raidGroups']);

        $characterFields = [
            'characters.id',
            'characters.raid_group_id',
            'characters.name',
            'characters.slug',
            'characters.level',
            'characters.race',
            'characters.spec',
            'characters.class',
            'characters.is_alt',
            'members.username',
            'raid_groups.name          AS raid_group_name',
            'raid_group_roles.color    AS raid_group_color',
            'added_by_members.username AS added_by_username',
        ];

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $items = Item::select(['items.*', 'guild_items.note AS guild_note', 'guild_items.priority AS guild_priority',])
            ->join('character_items',         'character_items.item_id', '=', 'items.item_id')
            ->join('characters',              'characters.id',           '=', 'character_items.character_id')
            ->leftJoin('item_item_sources', 'item_item_sources.item_id', '=', 'items.item_id')
            ->leftJoin('item_sources',      'item_sources.id',           '=', 'item_item_sources.item_source_id')
            ->leftJoin('guild_items', function ($join) use ($guild) {
                $join->on('guild_items.item_id', 'items.item_id')
                    ->where('guild_items.guild_id', $guild->id);
            })
            // First WHERE...
            ->where([
                ['characters.guild_id', $guild->id],
                ['items.expansion_id',  $guild->expansion_id],
            ])
            ->whereNull('characters.inactive_at')
            ->whereIn('character_items.type', [Item::TYPE_RECIPE])
            // Second WHERE...
            ->orWhere([
                ['characters.guild_id', $guild->id],
                ['items.expansion_id',  $guild->expansion_id],
            ])
            ->whereNull('characters.inactive_at')
            ->whereIn('character_items.type', [Item::TYPE_RECIPE])
            // Third WHERE...
            ->orWhereRaw("(`items`.`name` LIKE '%Design%'
                OR `items`.`name` LIKE '%Enchant%'
                OR `items`.`name` LIKE '%Formula%'
                OR `items`.`name` LIKE '%Pattern%'
                OR `items`.`name` LIKE '%Plans%'
                OR `items`.`name` LIKE '%Recipe%'
                OR `items`.`name` LIKE '%Schematic%')"
            )
            ->where([
                ['characters.guild_id', $guild->id],
                ['items.expansion_id',  $guild->expansion_id],
            ])
            ->whereNull('characters.inactive_at')
            ->whereIn('character_items.type', [Item::TYPE_RECIPE, Item::TYPE_RECEIVED])
            // End the WHERE's
            ->orderBy('items.name')
            ->groupBy('items.id')
            ->with([
                'receivedAndRecipeCharacters' => function ($query) use($guild) {
                    return $query->select([
                            'characters.id',
                            'characters.raid_group_id',
                            'characters.name',
                            'characters.slug',
                            'characters.level',
                            'characters.race',
                            'characters.spec',
                            'characters.class',
                            'characters.is_alt',
                            'members.username',
                            'raid_groups.name AS raid_group_name',
                            'raid_group_roles.color AS raid_group_color',
                            'added_by_members.username AS added_by_username',
                        ])
                        ->leftJoin('members', function ($join) {
                            $join->on('members.id', 'characters.member_id');
                        })
                        ->where([
                                ['characters.guild_id', $guild->id],
                            ])
                        ->groupBy(['character_items.character_id', 'character_items.item_id'])
                        ->orderBy('characters.name');
                }
            ])
            ->get();

            // Item IDs we don't want showing up, but would normally match our filters.
            $blackListItemIds = [63540, 65004];
            $items = $items->filter(function ($item) use ($blackListItemIds) {
                return !in_array($item->item_id, $blackListItemIds);
            });
        return view('item.listRecipes', [
            'currentMember'   => $currentMember,
            'guild'           => $guild,
            'items'           => $items,
            'showNotes'       => true,
            'showOfficerNote' => $showOfficerNote,
        ]);
    }
}
