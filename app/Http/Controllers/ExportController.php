<?php

namespace App\Http\Controllers;

use App;
use Exception;
use App\{Guild, GuildItem, Item};
use Illuminate\Support\Facades\{DB, Cache};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ExportController extends Controller {
    const CSV  = 'csv';
    const HTML = 'html';
    const JSON = 'json';

    const ADDON_HEADERS = [
        "type",
        "character_name",
        "character_class",
        "character_is_alt",
        "character_inactive_at",
        "character_note",
        "sort_order",
        "item_id",
        "is_offspec",
        // "officer_note",
        "received_at",
        "item_prio_note",
        "item_tier_label",
    ];

    const ATTENDANCE_HEADERS = [
        "raid_date",
        "raid_name",
        "character_name",
        "class",
        "credit",
        "is_alt",
        "remark",
        "character_inactive_at",
        "is_exempt",
        "member_name",
        "character_note",
        "raid_note",
        "instances",
        "raid_groups",
    ];


    const LOOT_HEADERS = [
        "type",
        "raid_group_name",
        "member_name",
        "character_name",
        "character_class",
        "character_is_alt",
        "character_inactive_at",
        "character_note",
        "sort_order",
        "item_name",
        "item_id",
        "is_offspec",
        "note",
        "received_at",
        "import_id",
        "item_note",
        "item_prio_note",
        "officer_note",
        "item_tier",
        "item_tier_label",
        "created_at",
        "updated_at",
        "instance_name",
        "source_name",
    ];

    const ITEM_NOTE_HEADERS = [
        "name",
        "id",
        "instance_name",
        "source_name",
        "guild_note",
        "prio_note",
        "officer_note",
        "tier",
        "tier_label",
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

    const RAID_GROUPS_HEADERS = [
        "raid_group_name",
        "status",
        "character_name",
        "character_class",
        "character_id",
        "member_name",
        "member_discord_username",
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
     * Export a guild's loot data for consumption in the TMB Helper addon
     *
     * @return Response
     */
    public function exportTmbHelperItems ($guildId, $guildSlug, $fileType)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $viewPrioPermission = $currentMember->hasPermission('view.prios');
        $showPrios = true;
        if ($guild->is_prio_private && !$viewPrioPermission) {
            $showPrios = false;
        }

        $showWishlist = true;
        if ($guild->is_wishlist_private && !$currentMember->hasPermission('view.wishlists')) {
            $showWishlist = false;
        }

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $itemOfficerNote = ($showOfficerNote ? "REPLACE(REPLACE(gi.officer_note, CHAR(13), ' '), CHAR(10), ' ')" : 'NULL');
        // $officerNote = ($showOfficerNote ? 'ci.officer_note' : 'NULL');

        $tierLabelField = $this->getTierLabelField($guild);
        $fields =
            "ci.type        AS 'type',
            c.name         AS 'character_name',
            c.class        AS 'character_class',
            c.is_alt       AS 'character_is_alt',
            c.inactive_at  AS 'character_inactive_at',
            REPLACE(REPLACE(c.public_note, CHAR(13), ' '), CHAR(10), ' ') AS 'character_note', -- remove newlines
            ci.`order`     AS 'sort_order',
            ci.item_id     AS 'item_id',
            ci.is_offspec  AS 'is_offspec',
            -- {officerNote} AS 'officer_note',
            ci.received_at AS 'received_at',
            REPLACE(REPLACE(gi.priority, CHAR(13), ' '), CHAR(10), ' ') AS 'item_prio_note',
            -- {$itemOfficerNote} AS 'item_officer_note',
            {$tierLabelField}";

        $rows = DB::select(DB::raw($this->getLootBaseSql('noRecipes', $guild, $showPrios, $showWishlist, $showOfficerNote, $viewPrioPermission, $fields)));

        $fields = "'item_note'    AS 'type',
            null           AS 'character_name',
            null           AS 'character_class',
            null           AS 'character_is_alt',
            null           AS 'character_inactive_at',
            null           AS 'character_note',
            null           AS 'sort_order',
            i.item_id      AS 'item_id',
            null           AS 'is_offspec',
            -- {officerNote} AS 'officer_note',
            null           AS 'received_at',
            REPLACE(REPLACE(gi.priority, CHAR(13), ' '), CHAR(10), ' ') AS 'item_prio_note', -- remove newlines
            -- REPLACE(REPLACE(gi.officer_note, CHAR(13), ' '), CHAR(10), ' ') AS 'item_officer_note', -- remove newlines
            {$tierLabelField}";

        $rows = array_merge(
            $rows,
            DB::select(DB::raw($this->getNotesBaseSql($guild, $showOfficerNote, $fields)))
        );

        $csv = $this->createCsv($rows, self::ADDON_HEADERS);

        return $this->getExport($csv, 'TMB Helper Export', $fileType);
    }

    /**
     * Export a guild's wishlist and loot data for the Gargul addon
     *
     * @return Response|View
     * @throws Exception
     */
    public function gargul()
    {
        $this->validate(request(), [
            'gargul_wishlist' => 'array|max:' . CharacterLootController::MAX_WISHLIST_LISTS,
            'gargul_wishlist.*' => 'integer|min:1|max:' . CharacterLootController::MAX_WISHLIST_LISTS,
            'raw' => 'boolean|nullable',
        ]);

        $raw = request()->get('raw');
        $guild = request()->get('guild');
        $currentMember = request()->get('currentMember');
        $memberCanViewPrios = !$guild->is_prio_private || $currentMember->hasPermission('view.prios');
        $memberCanViewWishlists = !$guild->is_wishlist_private || $currentMember->hasPermission('view.wishlists');

        // The current user doesn't have any of the required permissions so we only return the item notes.
        // Individual permissions are inspected separately further down.
        if (!$memberCanViewPrios && !$memberCanViewWishlists) {
            $payload = array_merge(['wishlists' => []], $this->gargulItemNotes($guild));
            return $this->getExport(
                json_encode($payload, JSON_UNESCAPED_UNICODE),
                'Gargul data',
                self::HTML
            );
        }

        $listNumbers = request()->input('gargul_wishlist') ?: [$guild->current_wishlist_number];

        $characters = $guild->characters()
            ->has('outstandingItems')
            ->with([
                'raidGroup' => function ($query) {
                    $query->select('id', 'name');
                },
                'outstandingItems' => function ($query) use ($guild, $listNumbers) {
                    return $query->where(function($query) use ($guild, $listNumbers) {
                        return $query
                            ->whereIn('list_number', $listNumbers)
                            ->orWhere(function ($query) {
                                $query->where('type', 'prio')
                                    ->where('is_received', 0);
                            })
                            ->select('character_id', 'item_id', 'type', 'order', 'is_offspec');
                    });
                },
            ])
            ->select('id', 'raid_group_id', 'name')
            ->get();

        $wishlistData = [];
        $raidGroupData = [];
        foreach ($characters as $character) {
            foreach ($character->outstandingItems as $item) {
                // The current member is not allowed to see the order of this item
                if (($item->type === Item::TYPE_PRIO && !$memberCanViewPrios)
                    || ($item->type === Item::TYPE_WISHLIST && !$memberCanViewWishlists)
                ) {
                    continue;
                }

                $itemId = $item->item_id;
                $characterName = mb_strtolower($character->name);

                if (!isset($wishlistData[$itemId])) {
                    $wishlistData[$itemId] = [];
                }

                $wishlistData[$itemId][] = sprintf(
                    '%s%s|%s|%s|%s',
                    $characterName,
                    $item->is_offspec ? '(OS)' : '',
                    $item->order,
                    $item->type === Item::TYPE_PRIO ? 1 : 2,
                    $character->raid_group_id ?: 0,
                );

                if ($character->raid_group_id) {
                    $raidGroupData[$character->raid_group_id] = $character->raidGroup->name;
                }
            }
        }

        $payload = array_merge([
            'wishlists' => $wishlistData,
            'groups' => $raidGroupData,
        ], $this->gargulItemNotes($guild));
        $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);

        if (!$raw) {
            return view('guild.export.gargul', [
                'currentMember' => $currentMember,
                'data' => base64_encode(gzcompress($payload, 9)),
                'guild' => $guild,
                'name' => 'Gargul data',
                'maxWishlistLists' => CharacterLootController::MAX_WISHLIST_LISTS,
                'showNotes' => true,
            ]);
        }

        return $this->getExport(
            $payload,
            'Gargul data',
            self::HTML
        );
    }

    /**
     * Export a guild's loot notes for the Gargul addon
     *
     * The reason why 'loot' is a CSV instead of JSON is because Gargul already
     * supported CSV loot priority strings before becoming compatible with TMB
     *
     * @param Guild $guild
     * @return array
     */
    private function gargulItemNotes(Guild $guild): array
    {
        $items = GuildItem::where(function ($query) {
                $query->whereNotNull('priority')
                    ->orWhereNotNull('note')
                    ->orWhereNotNull('tier');
            })
            ->where('guild_id', $guild->id)
            ->select('item_id', 'priority', 'note', 'tier')
            ->get();

        $notes = [];
        $tiers = [];
        $itemPriorityString = "";
        foreach ($items as $item) {
            $priority = trim($item->priority);
            $note = trim($item->note);
            $tier = $item->tier;

            if ($tier && $guild->tier_mode == 's') {
                $tier = Guild::TIERS[$item->tier] ?? null;
            }

            if ($priority) {
                $itemPriorityString .= "{$item->item_id} > {$priority}\n";
            }

            if ($note) {
                $notes[$item->item_id] = $note;
            }

            if ($tier) {
                $tiers[$item->item_id] = $tier;
            }
        }

        return [
            'loot' => $itemPriorityString,
            'notes' => $notes,
            'tiers' => $tiers,
        ];
    }

    /**
     * Export attendance data for all characters in the guild.
     *
     * @return Response
     */
    public function exportAttendance($guildId, $guildSlug, $fileType)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $csv = Cache::remember('attendanceExport:' . $guild->id, env('EXPORT_CACHE_SECONDS', 10), function () use ($guild) {
                $rows = DB::select(DB::raw(
                    sprintf("SELECT
                        r.date    AS 'raid_date',
                        r.name    AS 'raid_name',
                        c.name    AS 'character_name',
                        c.class   AS 'class',
                        rc.credit AS 'credit',
                        c.is_alt  AS 'is_alt',
                        CASE
                            -- See Raid.php for remarks
                            WHEN rc.remark_id = 1 THEN 'Late'
                            WHEN rc.remark_id = 2 THEN 'Unprepared'
                            WHEN rc.remark_id = 3 THEN 'Late & unprepared'
                            WHEN rc.remark_id = 4 THEN 'No call, no show'
                            WHEN rc.remark_id = 5 THEN 'Gave notice'
                            WHEN rc.remark_id = 6 THEN 'Benched'
                        END AS 'remark',
                        c.inactive_at  AS 'character_inactive_at',
                        rc.is_exempt   AS 'is_exempt',
                        m.username     AS 'member_name',
                        rc.public_note AS 'character_note',
                        r.public_note  AS 'raid_note',
                        (
                            SELECT GROUP_CONCAT(i.name SEPARATOR ', ')
                            FROM raid_instances AS ri
                            JOIN instances AS i ON i.id = ri.instance_id
                            WHERE ri.raid_id = r.id
                        ) AS 'instances',
                        (
                            SELECT GROUP_CONCAT(rg.name SEPARATOR ', ')
                            FROM raid_raid_groups AS rrg
                            JOIN raid_groups AS rg ON rg.id = rrg.raid_group_id
                            WHERE rrg.raid_id = r.id
                        ) AS 'raid_groups'
                    FROM `raids` r
                    JOIN `raid_characters` rc ON rc.raid_id = r.id
                    JOIN `characters` c       ON c.id = rc.character_id
                    JOIN `members` m          ON m.id = c.member_id
                    WHERE r.guild_id = %s
                        AND r.cancelled_at IS NULL
                        AND r.ignore_attendance = 0
                    ORDER BY r.date DESC, c.name ASC;", $guild->id)));

                return $this->createCsv($rows, self::ATTENDANCE_HEADERS);
            });

        return $this->getExport($csv, $guild->name . ' Attendance', $fileType);
    }

    /**
     * Export a guild's loot data
     *
     * @return Response
     */
    public function exportCharactersWithItems($guildId, $guildSlug, $fileType)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

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

        $characters = Cache::remember('export:roster:guild:' . $guild->id . ':showOfficerNote:' . $showOfficerNote . ':showPrios:' . $showPrios . ':viewPrioPermission:' . $viewPrioPermission . ':showWishlist:' . $showWishlist . ':attendance:' . $guild->is_attendance_hidden,
            env('EXPORT_CACHE_SECONDS', 120),
            function () use ($guild, $showOfficerNote, $showPrios, $showWishlist, $viewPrioPermission) {
            return $guild->getCharactersWithItemsAndPermissions($showOfficerNote, $showPrios, $showWishlist, $viewPrioPermission, false, false, null)['characters']->makeVisible('officer_note');
        });

        return $this->getExport($characters, 'Character JSON', $fileType);
    }

    /**
     * Export an expansion's loot tables
     *
     * @return Response
     */
    public function exportExpansionLoot($expansionSlug, $fileType)
    {
        if ($expansionSlug == 'classic') {
            $expansionId = 1;
        } else if ($expansionSlug == 'burning-crusade') {
            $expansionId = 2;
        } else if ($expansionSlug == 'wrath') {
            $expansionId = 3;
        } else if ($expansionSlug == 'season-of-discovery') {
            $expansionId = 4;
        } else if ($expansionSlug == 'cataclysm') {
            $expansionId = 5;
        } else if ($expansionSlug == 'mop') {
            $expansionId = 6;
        } else {
            abort(404, __('Expansion not found'));
        }

        $subdomain = 'www';
        if ($expansionId == 1 || $expansionId == 4) {
            $subdomain = 'classic';
        } else if ($expansionId == 2) {
            $subdomain = 'tbc';
        } else if ($expansionId == 3) {
            $subdomain = 'wotlk';
        } else if ($expansionId == 5) {
            $subdomain = 'cata';
        } else if ($expansionId == 6) {
            $subdomain = 'mop';
        }

        $locale = App::getLocale();
        if ($locale === 'en') {
            $locale = '';
        } else {
            $locale .= '.';
        }

        $csv = Cache::remember('lootTableExport:' . $expansionSlug, env('PUBLIC_EXPORT_CACHE_SECONDS', 600), function () use ($subdomain, $expansionId, $locale) {
                if ($expansionId === 3 || $expansionId === 5) {
                    $wowheadLink = "https://{$locale}wowhead.com/{$subdomain}/item=";
                } else {
                    $wowheadLink = "https://{$locale}{$subdomain}.wowhead.com/item=";
                }

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
                            WHEN items.quality = 7 THEN 'heirloom'
                        END AS 'item_quality',
                        -- items.quality AS 'item_quality'
                        items.item_id AS 'item_id',
                        CONCAT('{$wowheadLink}', items.item_id) AS 'url'
                    FROM instances
                        JOIN item_sources      ON item_sources.instance_id = instances.id
                        JOIN item_item_sources ON item_item_sources.item_source_id = item_sources.id
                        JOIN items             ON items.item_id = item_item_sources.item_id
                    WHERE items.expansion_id = {$expansionId}
                        AND instances.expansion_id = {$expansionId}
                    ORDER BY instances.`order` DESC, item_sources.`order` ASC, items.name ASC;"));

                return $this->createCsv($rows, self::EXPANSION_LOOT_HEADERS);
            });

        return $this->getExport($csv, $expansionSlug . ' Loot Table', $fileType);
    }

    /**
     * Export a guild's loot data
     *
     * @return Response
     */
    public function exportGuildLoot($guildId, $guildSlug, $fileType, $lootType)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $viewPrioPermission = $currentMember->hasPermission('view.prios');
        $showPrios = true;
        if ($guild->is_prio_private && !$viewPrioPermission) {
            $showPrios = false;
        }

        $showOfficerNote = false;
        if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
            $showOfficerNote = true;
        }

        $showWishlist = true;
        if ($guild->is_wishlist_private && !$currentMember->hasPermission('view.wishlists')) {
            $showWishlist = false;
        }

        if ($lootType == Item::TYPE_WISHLIST && !$showWishlist) {
            request()->session()->flash('status', __("You don't have permissions to view wishlists."));
            return redirect()->route('guild.home', ['guildId' => $guild->id, 'guildSlug' => $guildSlug]);
        }

        if ($lootType == Item::TYPE_PRIO && !$showPrios) {
            request()->session()->flash('status', __("You don't have permissions to view prios."));
            return redirect()->route('guild.home', ['guildId' => $guild->id, 'guildSlug' => $guildSlug]);
        }

        // $showOfficerNote = false;
        // if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
        //     $showOfficerNote = true;
        // }

        // $officerNote = ($showOfficerNote ? 'ci.officer_note' : 'NULL');

        $csv = Cache::remember(
            "export:{$lootType}:guild:{$guild->id}:showPrios:{$showPrios}:showOfficerNote:{$showOfficerNote}:viewPrioPermission:{$viewPrioPermission}:showWishlist:{$showWishlist}:file:{$fileType}",
            env('EXPORT_CACHE_SECONDS', 120),
            function () use ($lootType, $guild, $showPrios, $showWishlist, $showOfficerNote, $viewPrioPermission) {
                $rows = DB::select(DB::raw($this->getLootBaseSql($lootType, $guild, $showPrios, $showWishlist, $showOfficerNote, $viewPrioPermission)));

                if ($lootType == 'all') {
                    $rows = array_merge(
                        $rows,
                        DB::select(DB::raw($this->getNotesBaseSql($guild, $showOfficerNote)))
                    );
                }

                return $this->createCsv($rows, self::LOOT_HEADERS);
            });

        return $this->getExport($csv, ($lootType == 'all' ? 'All Loot' : ucfirst($lootType) ) . ' Export', $fileType);
    }

    /**
     * Export a guild's item notes and prio notes
     *
     * @return Response
     */
    public function exportItemNotes($guildId, $guildSlug, $fileType)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $csv = Cache::remember("export:notes:guild:{$guild->id}:file:{$fileType}", env('EXPORT_CACHE_SECONDS', 120), function () use ($guild, $currentMember) {
            $showOfficerNote = false;
            if ($currentMember->hasPermission('view.officer-notes') && !isStreamerMode()) {
                $showOfficerNote = true;
            }

            $itemOfficerNoteFragment = '';
            if ($showOfficerNote) {
                $itemOfficerNoteFragment = "REPLACE(REPLACE(gi.officer_note, CHAR(13), ' '), CHAR(10), ' ')";
            } else {
                $itemOfficerNoteFragment = "null";
            }

            $tierLabelField = $this->getTierLabelField($guild);
            $rows = DB::select(DB::raw(
                "SELECT
                    i.name            AS 'item_name',
                    i.item_id         AS 'item_id',
                    instances.name    AS 'instance_name',
                    item_sources.name AS 'source_name',
                    REPLACE(REPLACE(gi.note , CHAR(13), ' '), CHAR(10), ' ') AS 'item_note', -- remove newlines
                    REPLACE(REPLACE(gi.priority, CHAR(13), ' '), CHAR(10), ' ') AS 'item_prio_note', -- remove newlines
                    {$itemOfficerNoteFragment} AS 'item_officer_note',
                    gi.tier           AS 'tier',
                    {$tierLabelField},
                    gi.created_at     AS 'created_at',
                    gi.updated_at     AS 'updated_at'
                FROM items i
                    JOIN item_item_sources iis ON iis.item_id = i.item_id
                    JOIN item_sources          ON item_sources.id = iis.item_source_id
                    JOIN instances             ON instances.id = item_sources.instance_id
                    LEFT JOIN guild_items gi   ON gi.item_id = i.item_id AND gi.guild_id = {$guild->id}
                WHERE i.expansion_id = {$guild->expansion_id}
                ORDER BY instances.`order` DESC, i.name ASC;"));

            return $this->createCsv($rows, self::ITEM_NOTE_HEADERS);
        });

        return $this->getExport($csv, 'Item Notes', $fileType);
    }

    /**
     * Export a guild's raid groups
     *
     * @return Response
     */
    public function exportRaidGroups($guildId, $guildSlug, $fileType, $raidGroupId = null)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $csv = Cache::remember("export:raidGroups:guild:{$guild->id}:file:{$fileType}:raidGroupId:{$raidGroupId}", env('EXPORT_CACHE_SECONDS', 120), function () use ($guild, $raidGroupId) {
            $raidGroupIdClause = $raidGroupId ? "AND raid_groups.id = {$raidGroupId}" : '';
            $rows = DB::select(DB::raw(
                "SELECT
                    raid_group_name,
                    status,
                    character_name,
                    character_class,
                    character_id,
                    member_name,
                    member_discord_username
                FROM
                    (
                    SELECT
                        raid_groups.name AS raid_group_name,
                        characters.name  AS character_name,
                        characters.class AS character_class,
                        characters.id    AS character_id,
                        members.username AS member_name,
                        users.discord_username AS member_discord_username,
                        CASE WHEN characters.inactive_at THEN 'archived' ELSE 'general' END AS status
                    FROM guilds
                        JOIN raid_groups ON raid_groups.guild_id = guilds.id
                        LEFT JOIN character_raid_groups ON character_raid_groups.raid_group_id = raid_groups.id
                        LEFT JOIN characters ON characters.id = character_raid_groups.character_id
                        LEFT JOIN members    ON members.id = characters.member_id
                        LEFT JOIN users      ON users.id = members.user_id
                    WHERE guilds.id = {$guild->id} {$raidGroupIdClause}
                    UNION
                    SELECT
                            raid_groups.name AS raid_group_name,
                            characters.name  AS character_name,
                            characters.class AS character_class,
                            characters.id    AS character_id,
                            members.username AS member_name,
                            users.discord_username AS member_discord_username,
                            CASE WHEN characters.inactive_at THEN 'archived' ELSE 'main' END AS status
                        FROM guilds
                            JOIN raid_groups ON raid_groups.guild_id = guilds.id
                            LEFT JOIN characters ON characters.raid_group_id = raid_groups.id
                            LEFT JOIN members    ON members.id = characters.member_id
                            LEFT JOIN users      ON users.id = members.user_id
                        WHERE guilds.id = {$guild->id} {$raidGroupIdClause}
                    ) raiders
                ORDER BY raid_group_name ASC, character_name ASC;"));

            return $this->createCsv($rows, self::RAID_GROUPS_HEADERS);
        });

        return $this->getExport($csv, 'Raid Groups', $fileType);
    }

    /**
     * Pass an array of data and headers, get back a CSV string.
     *
     * @var array $rows    A set of rows of data.
     * @var array $headers The headers to go at the top of the CSV.
     *
     * @return string A CSV of the header and rows that were passed in.
     */
    public static function createCsv($rows, $headers) {
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

    /**
     * Get a CSV or HTML of the export
     *
     * @return Application|ResponseFactory|Factory|Response|View
     * @var string $title
     * @var string $fileType 'csv', 'json', anything else = html
     *
     * @var array|string  $csv      The data.
     */
    public static function getExport($csv, $title, $fileType) {
        if ($fileType == self::CSV) {
            return response($csv)
                ->withHeaders([
                    'Content-Type'        => 'text/csv',
                    'Cache-Control'       => 'no-store, no-cache',
                    'Content-Disposition' => 'attachment; filename="' . slug($title) . '.csv"',
                ]);
        } else if ($fileType == self::JSON) {
            return response($csv)
                ->withHeaders([
                    'Content-Type'        => 'text/json',
                    'Cache-Control'       => 'no-store, no-cache',
                    'Content-Disposition' => 'attachment; filename="' . slug($title) . '.json"',
                ]);
        } else {
            return response($csv)
                ->withHeaders([
                    // Browser should not attempt to download
                    'X-Content-Type-Options' => 'nosniff',
                    'Content-Type'           => 'text/plain',
                    'Cache-Control'          => 'no-store, no-cache',
                ]);
        }
    }

    /**
     * Get the SQL used for the character loot exports
     *
     * @var string    $lootType The type of loot to fetch.
     * @var App/Guild $guild    The guild it belongs to.
     * @var bool      $showPrios
     * @var bool      $showWishlist
     * @var bool      $showOfficerNote Should officer notes on items be shown?
     * @var bool      $viewPrioPermission
     * @var string    $fields       The fields to SELECT. Used to override this function's default fields.
     *
     * @return The thing to present to the user.
     */
    private function getLootBaseSql($lootType, $guild, $showPrios = true, $showWishlist = true, $showOfficerNote = false, $viewPrioPermission = false, $fields = null) {
        $lootTypeFragment = "";

        if (!$showPrios) {
            $lootTypeFragment .= " ci.type != 'prio' AND";
        }

        if ($guild->prio_show_count && !$viewPrioPermission) {
            // NOTE: This does not respect the prio LIMIT and instead only shows the top two regardless of what is received.
            $lootTypeFragment .= " ((type = 'prio' AND ci.`order` <= {$guild->prio_show_count}) OR (type != 'prio')) AND";
        }

        if (!$showWishlist) {
            $lootTypeFragment .= " ci.type != 'wishlist' AND";
        }

        if ($lootType == "noRecipes") {
            $lootTypeFragment .= " ci.type IN('prio', 'wishlist', 'received') AND";
        } else if ($lootType != "all") {
            $lootTypeFragment .= " ci.type = '{$lootType}' AND";
        }

        if (!$fields) {
            $itemOfficerNoteFragment = '';
            if ($showOfficerNote) {
                $itemOfficerNoteFragment = "REPLACE(REPLACE(gi.officer_note, CHAR(13), ' '), CHAR(10), ' ')";
            } else {
                $itemOfficerNoteFragment = "null";
            }

            $tierLabelField = $this->getTierLabelField($guild);
            $fields =
                "ci.type        AS 'type',
                rg.name        AS 'raid_group_name',
                m.username     AS 'member_name',
                c.name         AS 'character_name',
                c.class        AS 'character_class',
                c.is_alt       AS 'character_is_alt',
                c.inactive_at  AS 'character_inactive_at',
                REPLACE(REPLACE(c.public_note, CHAR(13), ' '), CHAR(10), ' ') AS 'character_note', -- remove newlines
                ci.`order`     AS 'sort_order',
                i.name         AS 'item_name',
                ci.item_id     AS 'item_id',
                ci.is_offspec  AS 'is_offspec',
                REPLACE(REPLACE(ci.note, CHAR(13), ' '), CHAR(10), ' ') AS 'note',
                -- {officerNote} AS 'officer_note',
                ci.received_at AS 'received_at',
                ci.import_id   AS 'import_id',
                REPLACE(REPLACE(gi.note, CHAR(13), ' '), CHAR(10), ' ') AS 'item_note',
                REPLACE(REPLACE(gi.priority, CHAR(13), ' '), CHAR(10), ' ') AS 'item_prio_note',
                {$itemOfficerNoteFragment} AS 'item_officer_note',
                gi.tier        AS 'item_tier',
                {$tierLabelField},
                ci.created_at  AS 'created_at',
                ci.updated_at  AS 'updated_at',
                instances.name    AS 'instance_name',
                item_sources.name AS 'source_name'";
        }

        return
            "SELECT
                {$fields}
            FROM character_items ci
                JOIN characters c        ON c.id = ci.character_id
                LEFT JOIN members m      ON m.id = c.member_id
                LEFT JOIN raid_groups rg ON rg.id = c.raid_group_id
                JOIN items i             ON i.item_id = ci.item_id
                LEFT JOIN item_item_sources ON item_item_sources.item_id = i.item_id
                LEFT JOIN item_sources      ON item_sources.id = item_item_sources.item_source_id
                LEFT JOIN instances         ON instances.id = item_sources.instance_id
                LEFT JOIN guild_items gi    ON gi.item_id = i.item_id AND gi.guild_id = c.guild_id
            WHERE {$lootTypeFragment}
                c.guild_id = {$guild->id}
                AND c.inactive_at IS NULL
                AND i.expansion_id = {$guild->expansion_id}
                AND (ci.type != 'wishlist' OR (ci.type = 'wishlist' AND ci.list_number = {$guild->current_wishlist_number}))
            GROUP BY ci.id
            ORDER BY ci.type, rg.name, c.name, ci.`order`;";
    }

    /**
     * Get the SQL used for the guild note exports that can be combined with the character exports
     *
     * @var App/Guild $guild  The guild it belongs to.
     * @var bool      $showOfficerNote Should officer notes on items be shown?
     * @var string    $fields Custom fields that override the default ones.
     *
     * @return The thing to present to the user.
     */
    private function getNotesBaseSql($guild, $showOfficerNote = false, $fields = null) {
        if (!$fields) {
            $itemOfficerNoteFragment = '';

            if ($showOfficerNote) {
                $itemOfficerNoteFragment = "REPLACE(REPLACE(gi.officer_note, CHAR(13), ' '), CHAR(10), ' ')";
            } else {
                $itemOfficerNoteFragment = "null";
            }
            $tierLabelField = $this->getTierLabelField($guild);
            $fields = "'item_note'    AS 'type',
                null           AS 'raid_group_name',
                null           AS 'member_name',
                null           AS 'character_name',
                null           AS 'character_class',
                null           AS 'character_is_alt',
                null           AS 'character_inactive_at',
                null           AS 'character_note',
                null           AS 'sort_order',
                i.name         AS 'item_name',
                i.item_id      AS 'item_id',
                null           AS 'is_offspec',
                null           AS 'note',
                -- {officerNote} AS 'officer_note',
                null           AS 'received_at',
                null           AS 'import_id',
                REPLACE(REPLACE(gi.note , CHAR(13), ' '), CHAR(10), ' ') AS 'item_note', -- remove newlines
                REPLACE(REPLACE(gi.priority, CHAR(13), ' '), CHAR(10), ' ') AS 'item_prio_note', -- remove newlines
                {$itemOfficerNoteFragment} AS 'item_officer_note', -- remove newlines
                gi.tier        AS 'item_tier',
                {$tierLabelField},
                gi.created_at  AS 'created_at',
                gi.updated_at  AS 'updated_at'";
        }
        return
            "SELECT
                {$fields}
            FROM items i
                LEFT JOIN item_item_sources iis ON iis.item_id = i.item_id
                LEFT JOIN item_sources          ON item_sources.id = iis.item_source_id
                LEFT JOIN instances             ON instances.id = item_sources.instance_id
                JOIN guild_items gi        ON gi.item_id = i.item_id AND gi.guild_id = {$guild->id}
            WHERE i.expansion_id = {$guild->expansion_id}
            ORDER BY instances.`order` DESC, i.name ASC;";
    }

    /**
     * Based on the guild's settings, get the correct label for item tiers.
     *
     * @var App/Guild $guild    The guild it belongs to.
     *
     * @return The SQL select field clause to use.
     */
    private function getTierLabelField($guild) {
        if ($guild->tier_mode == Guild::TIER_MODE_S) {
            $tiers = Guild::tiers();
            return
                "CASE
                    WHEN gi.tier = 1 THEN '{$tiers[1]}'
                    WHEN gi.tier = 2 THEN '{$tiers[2]}'
                    WHEN gi.tier = 3 THEN '{$tiers[3]}'
                    WHEN gi.tier = 4 THEN '{$tiers[4]}'
                    WHEN gi.tier = 5 THEN '{$tiers[5]}'
                    WHEN gi.tier = 6 THEN '{$tiers[6]}'
                END AS 'item_tier_label'";
        } else if ($guild->tier_mode == Guild::TIER_MODE_NUM) {
            return "gi.tier AS 'item_tier_label'";
        } else {
            return "null AS 'item_tier_label'";
        }
    }
}
