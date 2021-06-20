<?php

namespace App\Http\Controllers;

use App\{AuditLog, Guild, Instance, Item};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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

        $instance = Instance::where('slug', $instanceSlug)->firstOrFail();

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

        $cacheKey = 'items:guild:' . $guild->id . ':instance:' . $instance->id . ':officer:' . ($showOfficerNote ? 1 : 0) . ':prios:' . ($showPrios ? 1 : 0) . ':wishlist:' . ($showWishlist ? 1 : 0) . ':attendance:' . $guild->is_attendance_hidden;

        if (request()->get('bustCache')) {
            Cache::forget($cacheKey);
        }

        $items = Cache::remember($cacheKey, env('CACHE_INSTANCE_ITEMS_SECONDS', 5), function () use ($guild, $instance, $currentMember, $characterFields, $showPrios, $showWishlist, $viewPrioPermission) {
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
                ->whereNull('items.parent_id')
                ->orderBy('item_sources.order')
                ->orderBy('items.name');

            if ($showPrios) {
                $query = $query->with([
                    ($guild->is_attendance_hidden ? 'priodCharacters' : 'priodCharactersWithAttendance') => function ($query) use ($guild, $characterFields, $viewPrioPermission) {
                        if ($guild->prio_show_count && !$viewPrioPermission) {
                            $query = $query->where([
                                ['character_items.order', '<=', $guild->prio_show_count],
                            ]);
                        }

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
                            ->groupBy(['character_items.character_id', 'character_items.raid_group_id']);
                    }
                ]);
            }

            if ($showWishlist) {
                $query = $query->with([
                    ($guild->is_attendance_hidden ? 'wishlistCharacters' : 'wishlistCharactersWithAttendance') => function ($query) use($guild, $characterFields) {
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
                            ->groupBy(['character_items.character_id', 'character_items.item_id'])
                            ->orderBy('character_items.order');
                    },
                    'childItems' => function ($query) use ($guild) {
                        return $query->with([
                            ($guild->is_attendance_hidden ? 'wishlistCharacters' : 'wishlistCharactersWithAttendance') => function ($query) use($guild) {
                                return $query
                                    ->where([
                                        ['characters.guild_id', $guild->id],
                                    ])
                                ->groupBy(['character_items.character_id', 'character_items.item_id'])
                                ->orderBy('character_items.order');
                            },
                        ]);
                    }
                ]);
            } else {
                $query = $query->with(['childItems']);
            }

            $query = $query->with([
                    'receivedAndRecipeCharacters' => function ($query) use($guild) {
                        return $query->where(['characters.guild_id' => $guild->id]);
                    },
                ]);

            $items = $query->get();

            if ($showWishlist) {
                $items = $this->mergeTokenWishlists($items, $guild);
            }

            return $items;
        });

        return view('item.list', [
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
        if (!$guild->is_prio_private || $viewPrioPermission) {
            $showPrios = true;
        }

        $showWishlist = false;
        if (!$guild->is_wishlist_private || $currentMember->hasPermission('view.wishlists')) {
            $showWishlist = true;
        }

        $cacheKey = 'item:guild:' . $guild->id . 'item:' . $id . ':officer:' . ($showOfficerNote ? 1 : 0) . ':attendance:' . $guild->is_attendance_hidden;

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
                    ($guild->is_attendance_hidden ? 'priodCharacters' : 'priodCharactersWithAttendance') => function ($query) use ($guild, $viewPrioPermission) {
                        if ($guild->prio_show_count && !$viewPrioPermission) {
                            $query = $query->where([
                                ['character_items.order', '<=', $guild->prio_show_count],
                            ]);
                        }

                        return $query
                            ->where(['characters.guild_id' => $guild->id])
                            ->groupBy(['character_items.character_id', 'character_items.raid_group_id']);
                    },
                ]);
            }

            if ($showWishlist) {
                $query = $this->addWishlistQuery($query, $guild);
                $query = $query->with([
                'childItems' => function ($query) use ($guild) {
                    return $this->addWishlistQuery($query, $guild);
                }]);
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

            if ($showWishlist) {
                $items = $this->mergeTokenWishlists($items, $guild);
            }

            return $items->first();
        });

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
        $notes['note']       = null;
        $notes['priority']   = null;
        $notes['tier']       = null;

        // If this guild has notes for this item, prep them for ease of access in the view
        if ($item->guilds->count() > 0) {
            $notes['note']       = $item->guilds->first()->note;
            $notes['priority']   = $item->guilds->first()->priority;
            $notes['tier']       = $item->guilds->first()->tier;
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
        if ($guild->is_attendance_hidden && $item->relationLoaded('priodCharacters')) {
            $priodCharacters = $item->priodCharacters;
        } else if ($item->relationLoaded('priodCharactersWithAttendance')) {
            $priodCharacters = $item->priodCharactersWithAttendance;
        }

        $wishlistCharacters = null;
        if ($guild->is_attendance_hidden && $item->relationLoaded('wishlistCharacters')) {
            $wishlistCharacters = $item->wishlistCharacters;
        } else if ($item->relationLoaded('wishlistCharactersWithAttendance')) {
            $wishlistCharacters = $item->wishlistCharactersWithAttendance;
        }

        return view('item.show', [
            'currentMember'               => $currentMember,
            'guild'                       => $guild,
            'item'                        => $item,
            'notes'                       => $notes,
            'priodCharacters'             => $priodCharacters,
            'raidGroups'                  => $guild->raidGroups,
            'receivedAndRecipeCharacters' => $item->receivedAndRecipeCharacters,
            'showEdit'                    => $showEdit,
            'showNoteEdit'                => $showNoteEdit,
            'showOfficerNote'             => $showOfficerNote,
            'showPrioEdit'                => $showPrioEdit,
            'showPrios'                   => $showPrios,
            'showWishlist'                => $showWishlist,
            'wishlistCharacters'          => $wishlistCharacters,
            'itemJson'                    => self::getItemWowheadJson($guild->expansion_id, $item->item_id),
        ]);
    }

    private function addWishlistQuery($query, $guild) {
        return $query->with([
            ($guild->is_attendance_hidden ? 'wishlistCharacters' : 'wishlistCharactersWithAttendance') => function ($query) use($guild) {
                return $query
                    ->where([
                        ['characters.guild_id', $guild->id],
                        ['character_items.is_received', 0],
                    ])
                    ->groupBy(['character_items.character_id'])
                    ->with([
                        'prios',
                        'received',
                        'recipes',
                        'wishlist',
                    ]);
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
        }

        try {
            // Suppressing warnings with the error control operator @ (if the id doesn't exist, it will fail to open stream)
            $json = json_decode(file_get_contents('https://' . $domain . '.wowhead.com/tooltip/item/' . (int)$itemId));

            // Fix link - Not using this because I wasn't easily able to get wowhead's script to not parse the link and do stupid crap to it
            $json->tooltip = str_replace('href="/', 'href="https://' . $domain . '.wowhead.com/', $json->tooltip);

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
        // Merge items' child items' wishlist characters into parent items' wishlist characters
        foreach ($items->filter(function ($item, $key) { return $item->childItems->count(); }) as $item) {
            if ($guild->is_attendance_hidden) {
                foreach ($item->childItems->filter(function ($childItem, $key) { return $childItem->wishlistCharacters->count(); }) as $childItem) {
                    $items->where('id', $item->id)->first()->setRelation('wishlistCharacters', $items->where('id', $item->id)->first()->wishlistCharacters->merge($childItem->wishlistCharacters)->sortBy('pivot.order')->values());
                }
            } else {
                foreach ($item->childItems->filter(function ($childItem, $key) { return $childItem->wishlistCharactersWithAttendance->count(); }) as $childItem) {
                    $items->where('id', $item->id)->first()->setRelation('wishlistCharactersWithAttendance', $items->where('id', $item->id)->first()->wishlistCharactersWithAttendance->merge($childItem->wishlistCharactersWithAttendance)->sortBy('pivot.order')->values());
                }
            }
        }
        return $items;
    }
}
