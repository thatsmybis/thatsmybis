<?php

namespace App\Http\Controllers;

use App\{AuditLog, Character, Guild, Instance, Item};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{App, Cache, DB, Validator};
use Illuminate\Validation\Rule;

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

        $guild->load(['raidGroups']);

        $instance = Instance::where('slug', $instanceSlug)->with('itemSources')->firstOrFail();

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
            'users.discord_username',
            'raid_groups.name          AS raid_group_name',
            'raid_group_roles.color    AS raid_group_color',
            'added_by_members.username AS added_by_username',
        ];

        $viewPrioPermission = $currentMember->hasPermission('view.prios');
        $viewOfficerNotesPermission = $currentMember->hasPermission('view.officer-notes');

        $showOfficerNote = false;
        if ($viewOfficerNotesPermission && !isStreamerMode()) {
            $characterFields[] = 'characters.officer_note';
            $showOfficerNote = true;
        }

        $showPrios = false;
        if (!$guild->is_prio_disabled && (!$guild->is_prio_private || $viewPrioPermission)) {
            $showPrios = true;
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_disabled && (!$guild->is_wishlist_private || $currentMember->hasPermission('view.wishlists'))) {
            $showWishlist = true;
        }

        $cacheKey = 'items:guild:' . $guild->id . ':instance:' . $instance->id . ':officer:' . ($showOfficerNote ? 1 : 0) . ':prios:' . ($showPrios ? 1 : 0) . ':wishlist:' . ($showWishlist ? 1 : 0);

        if (request()->get('bustCache')) {
            Cache::forget($cacheKey);
        }

        $items = Cache::remember($cacheKey, env('CACHE_INSTANCE_ITEMS_SECONDS', 5), function () use ($guild, $instance, $currentMember, $characterFields, $showPrios, $showWishlist, $showOfficerNote, $viewPrioPermission) {
            $query = Item::select([
                    'items.id',
                    'items.item_id',
                    'items.name',
                    'items.quality',
                    'item_sources.name    AS source_name',
                    'item_sources.slug    AS source_slug',
                    'guild_items.note     AS guild_note',
                    'guild_items.priority AS guild_priority',
                    ($showOfficerNote ? 'guild_items.officer_note AS guild_officer_note' : DB::raw('null AS guild_officer_note')),
                    'guild_items.tier     AS guild_tier',
                ])
                ->leftJoin('item_item_sources', 'item_item_sources.item_id', '=', 'items.item_id')
                ->leftJoin('item_sources', 'item_sources.id', '=', 'item_item_sources.item_source_id')
                ->leftJoin('guild_items', function ($join) use ($guild) {
                    $join->on('guild_items.item_id', 'items.item_id')
                        ->where('guild_items.guild_id', $guild->id);
                })
                ->where([
                    ['item_sources.instance_id', $instance->id],
                    ['items.expansion_id', $guild->expansion_id],
                ])
                // ->whereNull('items.parent_id')
                ->orderBy('item_sources.order')
                ->orderBy('items.name')
                ->ofFaction($guild->faction);

            if ($showPrios) {
                $query = $query->with([
                    'priodCharacters' => function ($query) use ($guild, $characterFields, $viewPrioPermission) {
                        return $query
                            ->addSelect($characterFields)
                            ->leftJoin('members', function ($join) {
                                $join->on('members.id', 'characters.member_id');
                            })
                            ->leftJoin('users', function ($join) {
                                $join->on('users.id', 'members.user_id');
                            })
                            ->where([
                                ['characters.guild_id', $guild->id],
                                ['character_items.is_received', 0],
                            ])
                            ->whereIn('character_items.raid_group_id', $guild->raidGroups->pluck('id'))
                            ->whereNull('character_items.received_at')
                            ->groupBy(['character_items.character_id', 'character_items.item_id']);
                    }
                ]);
            }

            if ($showWishlist) {
                $query = $query->with([
                    'wishlistCharacters' => function ($query) use($guild, $characterFields) {
                        return $query
                            ->addSelect($characterFields)
                            ->leftJoin('members', function ($join) {
                                $join->on('members.id', 'characters.member_id');
                            })
                            ->leftJoin('users', function ($join) {
                                $join->on('users.id', 'members.user_id');
                            })
                            ->where([
                                    ['characters.guild_id', $guild->id],
                                    ['character_items.is_received', 0],
                                ])
                            ->whereNull('character_items.received_at')
                            ->groupBy(['character_items.character_id', 'character_items.item_id', 'character_items.list_number'])
                            ->orderBy('raid_group_name')
                            ->orderBy('character_items.order');

                    },
                    'childItems' => function ($query) use ($guild) {
                        return $query
                            ->ofFaction($guild->faction)
                            ->with([
                                'wishlistCharacters' => function ($query) use($guild) {
                                    return $query
                                        ->where([
                                            ['characters.guild_id', $guild->id],
                                            ['character_items.is_received', 0],
                                        ])
                                    ->whereNull('character_items.received_at')
                                    ->groupBy(['character_items.character_id', 'character_items.item_id', 'character_items.list_number'])
                                    ->orderBy('raid_group_name')
                                    ->orderBy('character_items.order');
                                },
                            ]);
                    }
                ]);
            } else {
                $query = $query->with('childItems', function ($query) use ($guild) {
                    return $query->ofFaction($guild->faction);
                });
            }

            $query = $query->with([
                    'receivedAndRecipeCharacters' => function ($query) use($guild) {
                        return $query->where(['characters.guild_id' => $guild->id]);
                    },
                ]);

            $items = $query->get();

            if ($guild->prio_show_count && !$viewPrioPermission) {
                $items->map(function ($item) use ($guild) {
                    $item = $this->filterItemPriodCharactersByGuildLimit($item, $guild);
                    return $item;
                });
            }

            if ($showWishlist) {
                $items = $this->mergeTokenWishlists($items, $guild);
            }

            return $items;
        });

        // For optimization, fetch characters with their attendance here.
        // We will plop this in with characters in the Javascript on the client side.
        $charactersWithAttendance = Guild::getAllCharactersWithAttendanceCached($guild);

        return view('item.list', [
            'charactersWithAttendance' => $charactersWithAttendance,
            'currentMember'   => $currentMember,
            'guild'           => $guild,
            'instance'        => $instance,
            'items'           => $items,
            'raidGroups'      => $guild->raidGroups,
            'showNotes'       => true,
            'showOfficerNote' => $showOfficerNote,
            'showPrios'       => $showPrios,
            'showWishlist'    => $showWishlist,
            'viewPrioPermission'         => $viewPrioPermission,
            'viewOfficerNotesPermission' => $viewOfficerNotesPermission,
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

        $guild->load(['raidGroups']);

        $characterFields = [
            'characters.raid_group_id',
            'characters.name',
            'characters.level',
            'characters.race',
            'characters.spec',
            'characters.class',
            'members.username',
            'raid_groups.name AS raid_group_name',
            'raid_group_roles.color AS raid_group_color',
        ];

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $viewPrioPermission = $currentMember->hasPermission('view.prios');
        $showPrios = false;
        if (!$guild->is_prio_disabled && (!$guild->is_prio_private || $viewPrioPermission)) {
            $showPrios = true;
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_disabled && (!$guild->is_wishlist_private || $currentMember->hasPermission('view.wishlists'))) {
            $showWishlist = true;
        }

        $cacheKey = 'item:guild:' . $guild->id . 'item:' . $id . ':officer:' . ($showOfficerNote ? 1 : 0);

        if (request()->get('bustCache')) {
            Cache::forget($cacheKey);
        }

        $item = Cache::remember($cacheKey, env('CACHE_ITEM_SECONDS', 5), function () use ($id, $guild, $showPrios, $showWishlist, $viewPrioPermission) {
            $query = Item::where([
                    ['item_id', $id],
                    ['expansion_id', $guild->expansion_id],
                ])
                ->with([
                    'guilds' => function ($query) use($guild) {
                        return $query->select([
                            'guild_items.created_by',
                            'guild_items.updated_by',
                            'guild_items.note',
                            'guild_items.priority',
                            'guild_items.officer_note',
                            'guild_items.tier',
                        ])
                        ->where('guilds.id', $guild->id);
                    },
                    'itemSources',
                    'itemSources.instance',
                    'receivedAndRecipeCharacters' => function ($query) use($guild) {
                        return $query
                            ->where(['characters.guild_id' => $guild->id]);
                    },
                    'parentItem',
                ]);

            if ($showPrios) {
                $query = $query->with([
                    'priodCharacters' => function ($query) use ($guild, $viewPrioPermission) {
                        return $query
                            ->where(['characters.guild_id' => $guild->id])
                            ->whereIn('character_items.raid_group_id', $guild->raidGroups->pluck('id'))
                            ->groupBy(['character_items.character_id', 'character_items.raid_group_id']);
                    },
                ]);
            }

            if ($showWishlist) {
                $query = $this->addWishlistQuery($query, $guild, $viewPrioPermission);
                $query = $query->with([
                    'childItems' => function ($query) use ($guild, $viewPrioPermission) {
                        $query = $this->addWishlistQuery($query, $guild, $viewPrioPermission);
                        return $query->ofFaction($guild->faction);
                    }
                ]);
            } else {
                $query = $query->with(['childItems']);
            }

            $query = $query->with([
                'receivedAndRecipeCharacters' => function ($query) use($guild) {
                    return $query
                        ->where(['characters.guild_id' => $guild->id]);
                },
            ]);

            $items = $query->get();

            if ($showPrios && $guild->prio_show_count && !$viewPrioPermission) {
                $items->map(function ($item) use ($guild) {
                    $item = $this->filterItemPriodCharactersByGuildLimit($item, $guild);
                    return $item;
                });
            }

            if ($showWishlist) {
                $items = $this->mergeTokenWishlists($items, $guild);
            }

            return $items->first();
        });

        if (!$item) {
            abort(404, __('Item not found.'));
        }

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
        $notes['note']         = null;
        $notes['priority']     = null;
        $notes['officer_note'] = null;
        $notes['tier']         = null;

        // If this guild has notes for this item, prep them for ease of access in the view
        if ($item->guilds->count() > 0) {
            $notes['note']         = $item->guilds->first()->note;
            $notes['priority']     = $item->guilds->first()->priority;
            $notes['officer_note'] = $showOfficerNote ? $item->guilds->first()->officer_note : null;
            $notes['tier']         = $item->guilds->first()->tier;
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

        $priodCharacters = null;
        if ($item->relationLoaded('priodCharacters')) {
            $priodCharacters = $item->priodCharacters;
        }

        $wishlistCharacters = null;
        if ($showWishlist) {
            if ($item->relationLoaded('wishlistCharacters')) {
                $wishlistCharacters = $item->wishlistCharacters;
            }

            // For optimization, fetch characters with their attendance here and then merge them into
            // the existing characters for prios and wishlists
            if (!$guild->is_attendance_hidden && $wishlistCharacters) {
                $charactersWithAttendance = Guild::getAllCharactersWithAttendanceCached($guild);
                $wishlistCharacters = Character::mergeAttendance($wishlistCharacters, $charactersWithAttendance);
            }
        }

        if ($wishlistCharacters) {
            foreach ($wishlistCharacters as $character) {
                $wish = $character->allWishlists->where('item_id', $item->item_id)->first();
                if ($wish) {
                    $wish = $wish->pivot;
                    $character->roster_note_list_number = $wish->list_number;
                    $character->roster_note_order       = $wish->order;
                    $character->roster_note_is_offspec  = $wish->is_offspec;
                    $character->roster_note             = $wish->note;
                    $character->roster_note_date        = $wish->created_at;
                }
            }
        }

        return view('item.show', [
            'currentMember'   => $currentMember,
            'guild'           => $guild,
            'item'            => $item,
            'notes'           => $notes,
            'priodCharacters' => $priodCharacters,
            'raidGroups'      => $guild->raidGroups,
            'receivedAndRecipeCharacters' => $item->receivedAndRecipeCharacters,
            'showEdit'           => $showEdit,
            'showNoteEdit'       => $showNoteEdit,
            'showOfficerNote'    => $showOfficerNote,
            'showPrioEdit'       => $showPrioEdit,
            'showPrios'          => $showPrios,
            'showWishlist'       => $showWishlist,
            'wishlistCharacters' => $wishlistCharacters,
            'itemJson'           => self::getItemWowheadJson($guild->expansion_id, $item->item_id),
        ]);
    }

    private function addWishlistQuery($query, $guild, $viewPrioPermission) {
        return $query->with([
            'wishlistCharacters' => function ($query) use($guild, $viewPrioPermission) {
                $query = $query
                    ->where([
                        ['characters.guild_id', $guild->id],
                        ['character_items.is_received', 0],
                    ])
                    ->groupBy(['character_items.character_id'])
                    ->with([
                        'prios' => function ($query) use ($guild, $viewPrioPermission) {
                            if ($guild->prio_show_count && !$viewPrioPermission) {
                                $query = $query->where([
                                    ['character_items.order', '<=', $guild->prio_show_count],
                                ]);
                            }
                        },
                        'received',
                        'recipes',
                        'allWishlists',
                    ])
                    ->orderBy('character_items.order');

                return $query;
            },
        ]);
    }

    /**
     * Grab the JSON for an item from Wowhead, return only the HTML for the tooltip.
     *
     * @param int $id The ID of the item to fetch.
     */
    public static function getItemWowheadJson($expansionId, $itemId) {
        $json = null;
        $domain = 'www';

        if ($expansionId === 1) {
            $domain = 'classic';
        } else if ($expansionId === 2) {
            $domain = 'tbc';
        } else if ($expansionId === 3) {
            $domain = 'wotlk';
        }

        $locale = App::getLocale();
        if ($locale === 'en') {
            $locale = '';
        }

        try {
            if ($locale) {
                $json = json_decode(file_get_contents('https://nether.wowhead.com/' . $domain . '/' . $locale . '/tooltip/item/' . (int)$itemId));
            } else {
                $json = json_decode(file_get_contents('https://nether.wowhead.com/' . $domain . '/tooltip/item/' . (int)$itemId));
            }

            // Fix link - Not using this because I wasn't easily able to get wowhead's script to not parse the link and do stupid crap to it
            // $json->tooltip = str_replace('href="/', 'href="https://' . $locale . $domain . '.wowhead.com/', $json->tooltip);

            // Remove links
            $json->tooltip = str_replace('<a ', '<span ', $json->tooltip);
            $json->tooltip = str_replace('</a>', '</span>', $json->tooltip);
        } catch (Exception $e) {
            // Fail silently, that's okay, we just won't display the content
        }

        return $json;
    }

    /**
     * Take an array of items fetched fresh from the database. Items should have with('childItems', 'childItems.wishlists').
     * Merge all of the childItems' wishlists into their parents.
     * This causes tokens to show anyone who wishlisted the items they get turned in for... provided the DB has those relationships set up/inserted.
     */
    public static function mergeTokenWishlists($items, $guild) {
        // Combine items' child items' wishlist characters into parent items' wishlist characters
        foreach ($items->filter(function ($item) { return $item->childItems->count(); }) as $itemKey => $item) {
            foreach ($item->childItems->filter(function ($childItem) { return $childItem->wishlistCharacters->count(); }) as $childItem) {
                $items[$itemKey]
                    ->setRelation(
                        'wishlistCharacters',
                        $items[$itemKey]
                            ->wishlistCharacters
                            // Keys should be unique per character ID and wishlist number.
                            ->keyBy(function ($character) {return $character->id . '-' . $character->pivot->list_number;})
                            // Combine the two arrays based on the keys we just defined.
                            // If a character has both a token and its child items wishlisted, merge into one.
                            // But ONLY if they are on the same wishlist number.
                            ->union(
                                $childItem->wishlistCharacters->keyBy(function ($character) {return $character->id . '-' . $character->pivot->list_number;})
                            )
                            ->sortBy(function($character) {
                                // RE: -strtotime(): rofl, rofl, kekw, bur, kek, roflmao sort by newest to oldest date wishlisted.
                                return [$character->raid_group_name, $character->pivot->order, -strtotime($character->pivot->created_at)];
                            })
                            ->values()
                    );
            }
        }
        return $items;
    }

    /**
     * Based on the number of prios to show in the guild settings; filter related
     * prio'd characters to not exceed that limit; per raid group.
     *
     * @return Item $item The item with a filtered priod character list.
     */
    private function filterItemPriodCharactersByGuildLimit(Item $item, Guild $guild): Item
    {
        if ($item->priodCharacters->count() > 0) {
            // Return $guild->prio_show_count items per raid group
            $prioCountPerRaidGroup = [];
            $prioCountPerRaidGroup[0] = 0;
            foreach ($guild->raidGroups as $raidGroup) {
                $prioCountPerRaidGroup[$raidGroup->id] = 0;
            }

            $filteredPriodCharacters = $item->priodCharacters->filter(
                function ($priodCharacter) use ($guild, &$prioCountPerRaidGroup) {
                    $count = null;
                    if ($priodCharacter->pivot->raid_group_id) {
                        if (array_key_exists($priodCharacter->pivot->raid_group_id, $prioCountPerRaidGroup)) {
                            $prioCountPerRaidGroup[$priodCharacter->pivot->raid_group_id] = $prioCountPerRaidGroup[$priodCharacter->pivot->raid_group_id] + 1;
                            $count = $prioCountPerRaidGroup[$priodCharacter->pivot->raid_group_id];
                        } else {
                            // Raid group doesn't exist or is archived; don't show item
                            return false;
                        }
                    } else {
                        $prioCountPerRaidGroup[0] = $prioCountPerRaidGroup[0] + 1;
                        $count = $prioCountPerRaidGroup[0];
                    }
                    if ($count <= $guild->prio_show_count) {
                        return true;
                    } else {
                        return false;
                    }
                });

            $item->setRelation('priodCharacters', $filteredPriodCharacters->values());
        }
        return $item;
    }
}
