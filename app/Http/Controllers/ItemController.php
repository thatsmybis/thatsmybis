<?php

namespace App\Http\Controllers;

use App\{Guild, Item};
use Auth;
use Illuminate\Http\Request;

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
     * Show an item
     *
     * @return \Illuminate\Http\Response
     */
    public function showWithGuild($guildSlug, $id, $slug = null)
    {
        $guild = Guild::where('slug', $guildSlug)
            ->with(['members' => function ($query) {
                    return $query->where('members.id', Auth::id());
                        // Not grabbing member.user and member.user.roles here because the code is messier than just doing it in a separate call
                },
                'raids',
            ])
            ->firstOrFail();

        $item = Item::where('item_id', $id)->with([
                'characters' => function ($query) use($guild) {
                    return $query->where([
                            'characters.guild_id' => $guild->id,
                            'character_items.type' => 'wishlist',
                        ])
                    // ->whereNull('characters.id')
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

        if (!$guild->members->first()) {
            abort(403, "You're not part of that guild.");
        }

        $itemSlug = slug($item->name);

        if ($slug && $slug != $itemSlug) {
            return redirect()->route('guild.item.show', ['guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'slug' => slug($item->name)]);
        }

        return view('item', [
            'characters' => $item->characters,
            'guild'      => $guild,
            'item'       => $item,
            'raids'      => $guild->raids,
            'itemJson'   => self::getItemJson($item->item_id),
        ]);
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
