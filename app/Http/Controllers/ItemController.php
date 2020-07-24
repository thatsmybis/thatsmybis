<?php

namespace App\Http\Controllers;

use App\{Guild, Instance, Item};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function listWithGuild($guildSlug, $instanceSlug)
    {
        $guild = Guild::where('slug', $guildSlug)
            ->with(['members' => function ($query) {
                    return $query->where('members.user_id', Auth::id());
                },
                'raids',
            ])
            ->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        $instance = Instance::where('slug', $instanceSlug)->firstOrFail();

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
            ->orderBy('item_sources.order')
            ->orderBy('items.name')
            ->with(['wishlistCharacters' => function ($query) use($guild) {
                return $query->where('characters.guild_id', $guild->id)
                    ->groupBy(['character_items.character_id', 'character_items.item_id']);
                }
            ])
            ->get();

        // TODO: Permissions to view this guild's items

        return view('item.list', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'instance'      => $instance,
            'items'         => $items,
            'raids'         => $guild->raids,
        ]);
    }

    /**
     * Show the mass input page
     *
     * @return \Illuminate\Http\Response
     */
    public function massInput($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with([
            'characters',
            'members' => function ($query) {
                    return $query->where('members.user_id', Auth::id());
                },
            'raids',
            ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        // TODO: Keep this style of permissions check?
        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        // TODO: Validate user can view this guild's raids

        return view('item.massInput', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
        ]);
    }

    /**
     * Show an item
     *
     * @return \Illuminate\Http\Response
     */
    public function showWithGuild($guildSlug, $id, $slug = null)
    {
        $guild = Guild::where('slug', $guildSlug)
            ->with(['members' => function ($query) {
                    return $query->where('members.user_id', Auth::id());
                        // Not grabbing member.user and member.user.roles here because the code is messier than just doing it in a separate call
                },
                'raids',
            ])
            ->firstOrFail();

        $currentMember = $guild->members->first();

        if (!$currentMember) {
            abort(403, "You're not part of that guild.");
        }

        $item = Item::where('item_id', $id)->with([
            'guilds' => function ($query) use($guild) {
                return $query->select([
                    'guild_items.created_by',
                    'guild_items.updated_by',
                    'guild_items.note',
                    'guild_items.priority'
                ])
                ->where('guilds.id', $guild->id);
            },
            'receivedCharacters' => function ($query) use($guild) {
                return $query
                    ->where([
                        'characters.guild_id' => $guild->id,
                    ])
                    ->groupBy(['character_items.character_id'])
                    ->with([
                        'raid',
                        'received',
                        'recipes',
                        'wishlist',
                    ]);
            },
            'wishlistCharacters' => function ($query) use($guild) {
                return $query
                    ->where([
                        'characters.guild_id' => $guild->id,
                    ])
                    ->groupBy(['character_items.character_id'])
                    ->with([
                        'raid',
                        'received',
                        'recipes',
                        'wishlist',
                    ]);
            },
        ])->firstOrFail();

        // TODO: Permissions to view this guild's item entry..
        // I did a check here, not sure if it's what I'll use as a standard.
        // Leave it if it's fine, replace it if it's not.

        $itemSlug = slug($item->name);

        if ($slug && $slug != $itemSlug) {
            return redirect()->route('guild.item.show', [
                'guildSlug' => $guild->slug,
                'item_id' => $item->item_id,
                'slug' => slug($item->name)
            ]);
        }

        $notes = [];
        $notes['note']     = null;
        $notes['priority'] = null;

        // If this guild has notes for this item, prep them for ease of access in the view
        if ($item->guilds->count() > 0) {
            $notes['note']     = $item->guilds->first()->pivot->note;
            $notes['priority'] = $item->guilds->first()->pivot->priority;
        }

        return view('item.show', [
            'currentMember'      => $currentMember,
            'guild'              => $guild,
            'item'               => $item,
            'notes'              => $notes,
            'raids'              => $guild->raids,
            'receivedCharacters' => $item->receivedCharacters,
            'showNoteEdit'       => true, // TODO PERMISSIONS
            'wishlistCharacters' => $item->wishlistCharacters,
            'itemJson'           => self::getItemJson($item->item_id),
        ]);
    }

    public function submitMassInput($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                    return $query->where('members.user_id', Auth::id());
                },
            'characters',
            ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        // TODO: Keep this style of permissions check?
        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        $validationRules =  [
            'items.*.id'            => 'nullable|integer|exists:items,item_id',
            'items.*.character_id'  => 'nullable|integer|exists:characters,id',

        ];

        $this->validate(request(), $validationRules);

        // TODO: permissions for mass assigning items in this guild?

        $warnings   = '';
        $newRows    = [];
        $detachRows = [];
        $now        = getDateTime();

        $addedCount  = 0;
        $failedCount = 0;

        foreach (request()->input('items') as $item) {
            if ($item['id']) {
                if ($guild->characters->contains('id', $item['character_id'])) {
                    $newRows[] = [
                        'item_id'      => $item['id'],
                        'character_id' => $item['character_id'],
                        'added_by'     => $currentMember->id,
                        'type'         => Item::TYPE_RECEIVED,
                        'order'        => '0', // Top of the list
                        'created_at'   => $now,
                    ];
                    $detachRows[] = [
                        'item_id'      => $item['id'],
                        'character_id' => $item['character_id'],
                        'type'         => Item::TYPE_WISHLIST,
                    ];
                    $addedCount++;
                } else {
                    $warnings .= (isset($item['label']) ? $item['label'] : $item['id']) . ' to character ID ' . $item['character_id'] . ', ';
                    $failedCount++;
                }
            }
        }

        // Add the items to the character's received list
        DB::table('character_items')->insert($newRows);

        // For each item added, attempt to delete a matching item from the character's wishlist
        foreach ($detachRows as $detachRow) {
            DB::table('character_items')->where([
                'item_id'      => $detachRow['item_id'],
                'character_id' => $detachRow['character_id'],
                'type'         => $detachRow['type'],
            ])->limit(1)->delete();
        }

        request()->session()->flash('status', 'Successfully added ' . $addedCount . ' items. ' . $failedCount . ' failures' . ($warnings ? ': ' . rtrim($warnings, ', ') : '.'));

        return redirect()->route('guild.roster', ['guildSlug' => $guild->slug]);
    }

    /**
     * Update an item's notes
     * @return
     */
    public function updateNote($guildSlug) {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                return $query->where('members.user_id', Auth::id());
            },
            ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        $validationRules = [
            'id'       => 'required|integer|exists:items,item_id',
            'note'     => 'nullable|string|max:144',
            'priority' => 'nullable|string|max:144',
        ];

        $validationMessages = [];

        $this->validate(request(), $validationRules, $validationMessages);

        $item = Item::findOrFail(request()->input('id'));

        $existingRelationship = $guild->items()->find(request()->input('id'));

        // TODO: If has permissions to edit items for this guild
        if (false) {
            abort(403, "You do not have permission to edit someone else's character.");
        }

        $noticeVerb = null;

        if ($existingRelationship) {
            $noticeVerb = 'updated';

            $guild->items()->updateExistingPivot($item->item_id, [
                'note'       => request()->input('note'),
                'priority'   => request()->input('priority'),
                'updated_by' => $currentMember->id,
            ]);
        } else {
            $noticeVerb = 'created';

            $guild->items()->attach($item->item_id, [
                'note'       => request()->input('note'),
                'priority'   => request()->input('priority'),
                'created_by' => $currentMember->id,
            ]);
        }

        request()->session()->flash('status', "Successfully " . $noticeVerb . " " . $item->name ."'s note.");

        return redirect()->route('guild.item.show', ['guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'slug' => slug($item->name)]);
    }

    /**
     * Grab the JSON for an item from Wowhead, return only the HTML for the tooltip.
     *
     * @param int $id The ID of the item to fetch.
     */
    public static function getItemJson($id) {
        $json = null;
        try {
            // Suppressing warnings with the error control operator @ (if the id doesn't exist, it will fail to open stream)
            $json = json_decode(file_get_contents('https://classic.wowhead.com/tooltip/item/' . (int)$id));
        } catch (Exception $e) {
            // Fail silently, that's okay, we just won't display the content
        }
        return $json;
    }
}
