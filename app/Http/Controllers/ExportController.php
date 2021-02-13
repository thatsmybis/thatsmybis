<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    const WISHLIST_A_HEADERS = [
        "raid_name",
        "character_name",
        "character_class",
        "character_inactive_at",
        "sort_order",
        "item_name",
        "item_id",
        "note",
        // "officer_note",
        "received_at",
        "import_id",
        "created_at",
        "updated_at",
        "item_note",
        "item_prio_note",
    ];

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
     * Export a guild's wishlist data
     *
     * @return \Illuminate\Http\Response
     */
    public function exportWishlist($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $validTypes = [
            'csv'  => 'csv',
            'json' => 'csv', // TODO: do it later
        ];
        // Basic validation to ensure we get a valid type
        $type = (isset($validTypes[request()->get('type')]) ? $validTypes[request()->get('type')] : 'csv');

        if ($guild->is_wishlist_locked && !$currentMember->hasPermission('loot.characters')) {
            request()->session()->flash('status', 'You don\'t have permissions to view wishlists.');
            return redirect()->route('guild.home', ['guildId' => $guild->id, 'guildSlug' => $guildSlug]);
        }

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $officerNote = ($showOfficerNote ? 'ci.officer_note' : 'NULL');
        $rows = DB::select(DB::raw(
            "SELECT
                r.name AS 'raid_name',
                c.name AS 'character_name',
                c.class AS 'character_class',
                c.inactive_at AS 'character_inactive_at',
                ci.`order` AS 'sort_order',
                i.name AS 'item_name',
                ci.item_id AS 'item_id',
                ci.note AS 'note',
                -- {$officerNote} AS 'officer_note',
                ci.received_at AS 'received_at',
                ci.import_id AS 'import_id',
                ci.created_at AS 'created_at',
                ci.updated_at AS 'updated_at',
                gi.note AS 'item_note',
                gi.priority AS 'item_prio_note'
            FROM character_items ci
                JOIN characters c ON c.id = ci.character_id
                LEFT JOIN raids r ON r.id = c.raid_id
                JOIN items i ON i.item_id = ci.item_id
                LEFT JOIN guild_items gi ON gi.item_id = i.item_id AND gi.guild_id = c.guild_id
            WHERE ci.type = 'wishlist' AND c.guild_id = {$guild->id}
            ORDER BY r.name, c.name, ci.`order`;"));

        // output up to 5MB is kept in memory, if it becomes bigger it will automatically be written to a temporary file
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');

        fputcsv($csv, self::WISHLIST_A_HEADERS);
        foreach ($rows as $row) {
            // Convert from stdClass to array
            $row = get_object_vars($row);
            fputcsv($csv, $row);
        }
        rewind($csv);
        $csv = stream_get_contents($csv);

        return view('guild.export.generic', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'csv'           => $csv,
        ]);
    }
}
