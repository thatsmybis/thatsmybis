<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ExportController extends Controller {
    const LOOT_HEADERS = [
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
        "item_note",
        "item_prio_note",
        "created_at",
        "updated_at",
    ];

    const ITEM_NOTE_HEADERS = [
        "name",
        "id",
        "instance_name",
        "source_name",
        "guild_note",
        "prio_note",
        "created_at",
        "updated_at",
    ];

    const EXPANSION_LOOT_HEADERS = [
        "instance_name",
        "source_name",
        "item_name",
        "item_quality",
        "item_id",
        "url",
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser'])->except(['exportExpansionLoot']);
    }

    /**
     * Export a guild's loot data
     *
     * @return \Illuminate\Http\Response
     */
    public function exportCharactersWithItems($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $characters = $guild->getCharactersWithItemsAndPermissions($currentMember, false);

        return view('guild.export.generic', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'data'          => $characters['characters'],
            'name'          => 'Characters JSON',
        ]);
    }

    /**
     * Export an expansion's loot tables
     *
     * @return \Illuminate\Http\Response
     */
    public function exportExpansionLoot($expansionSlug)
    {
        if ($expansionSlug == 'classic') {
            $expansionId = 1;
        } else if ($expansionSlug == 'burning-crusade') {
            $expansionId = 2;
        }

        // TODO: Only Classic has valid links as of 2021-02-16. Update this when other expansions are supported.
        $subdomain = 'www';
        if ($expansionId == 1) {
            $subdomain = 'classic';
        }

        $csv = Cache::remember('lootTable:' . $expansionSlug, 600, function () use ($subdomain, $expansionId) {
                $rows = DB::select(DB::raw(
                    "SELECT
                        instances.name AS 'instance_name',
                        item_sources.name AS 'source_name',
                        items.name AS 'item_name',
                        CASE
                            WHEN items.quality = 1 THEN 'poor'
                            WHEN items.quality = 2 THEN 'common'
                            WHEN items.quality = 3 THEN 'rare'
                            WHEN items.quality = 4 THEN 'epic'
                            WHEN items.quality = 5 THEN 'legendary'
                            WHEN items.quality = 6 THEN 'artifact'
                            WHEN items.quality = 7 THEN 'hierloom'
                        END AS 'item_quality',
                        -- items.quality AS 'item_quality'
                        items.item_id AS 'item_id',
                        CONCAT('https://{$subdomain}.wowhead.com/item=', items.item_id) AS 'url'
                    FROM instances
                        JOIN item_sources      ON item_sources.instance_id = instances.id
                        JOIN item_item_sources ON item_item_sources.item_source_id = item_sources.id
                        JOIN items             ON items.item_id = item_item_sources.item_id
                    WHERE items.expansion_id = {$expansionId}
                    ORDER BY instances.`order` DESC, item_sources.`order` ASC, items.name ASC;"));

                return $this->createCsv($rows, self::EXPANSION_LOOT_HEADERS);
            });

        return view('guild.export.generic', [
            'data' => $csv,
            'name' => $expansionSlug . ' Loot Table CSV',
        ]);
    }

    /**
     * Export a guild's item notes and prio notes
     *
     * @return \Illuminate\Http\Response
     */
    public function exportItemNotes($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $rows = DB::select(DB::raw(
            "SELECT
                i.name AS 'item_name',
                i.item_id AS 'item_id',
                instances.name AS 'instance_name',
                item_sources.name AS 'source_name',
                gi.note AS 'item_note',
                gi.priority AS 'item_prio_note',
                gi.created_at AS 'created_at',
                gi.updated_at AS 'updated_at'
            FROM items i
                JOIN item_item_sources iis ON iis.item_id = i.item_id
                JOIN item_sources ON item_sources.id = iis.item_source_id
                JOIN instances ON instances.id = item_sources.instance_id
                LEFT JOIN guild_items gi ON gi.item_id = i.item_id AND gi.guild_id = {$guild->id}
            WHERE i.expansion_id = {$guild->expansion_id}
            ORDER BY instances.`order` DESC, i.name ASC;"));

        $csv = $this->createCsv($rows, self::ITEM_NOTE_HEADERS);

        return view('guild.export.generic', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'data'          => $csv,
            'name'          => 'Item Notes CSV',
        ]);
    }

    /**
     * Export a guild's loot data
     *
     * @return \Illuminate\Http\Response
     */
    public function exportLoot($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

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
            WHERE ci.type = 'received' AND c.guild_id = {$guild->id}
            ORDER BY r.name, c.name, ci.`order`;"));

        $csv = $this->createCsv($rows, self::LOOT_HEADERS);

        return view('guild.export.generic', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'data'          => $csv,
            'name'          => 'Loot CSV',
        ]);
    }

    /**
     * Export a guild's prio data
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPrios($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

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
            WHERE ci.type = 'prio' AND c.guild_id = {$guild->id}
            ORDER BY r.name, c.name, ci.`order`;"));

        $csv = $this->createCsv($rows, self::LOOT_HEADERS);

        return view('guild.export.generic', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'data'          => $csv,
            'name'          => 'Prios CSV',
        ]);
    }

    /**
     * Export a guild's wishlist data
     *
     * @return \Illuminate\Http\Response
     */
    public function exportWishlists($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

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

        $csv = $this->createCsv($rows, self::LOOT_HEADERS);

        return view('guild.export.generic', [
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'data'          => $csv,
            'name'          => 'Wishlist CSV',
        ]);
    }

    /**
     * Pass an array of data and headers, get back a CSV string.
     *
     * @var array $rows    A set of rows of data.
     * @var array $headers The headers to go at the top of the CSV.
     *
     * @return string A CSV of the header and rows that were passed in.
     */
    private function createCsv($rows, $headers) {
        // output up to 5MB is kept in memory, if it becomes bigger it will automatically be written to a temporary file
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');

        fputcsv($csv, $headers);
        foreach ($rows as $row) {
            // Convert from stdClass to array
            $row = get_object_vars($row);
            fputcsv($csv, $row);
        }
        rewind($csv);
        $csv = stream_get_contents($csv);

        return $csv;
    }
}
