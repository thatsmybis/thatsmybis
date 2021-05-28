<?php

namespace App;

use App\{Item, Guild, Member, Raid, RaidGroup};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Character extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'guild_id',
        'name',
        'slug',
        'level',
        'race',
        'class',
        'spec',
        'profession_1',
        'profession_2',
        'rank',
        'rank_goal',
        'raid_group_id',
        'public_note',
        'officer_note',
        'personal_note',
        'order',
        'inactive_at',
        'is_alt',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'officer_note',
        'personal_note',
    ];

    const RACE_BLOOD_ELF = 'Blood Elf';
    const RACE_ORC       = 'Orc';
    const RACE_TAUREN    = 'Tauren';
    const RACE_TROLL     = 'Troll';
    const RACE_UNDEAD    = 'Undead';
    const RACE_DRAENEI   = 'Draenei';
    const RACE_DWARF     = 'Dwarf';
    const RACE_GNOME     = 'Gnome';
    const RACE_HUMAN     = 'Human';
    const RACE_NIGHT_ELF = 'Night Elf';

    const CLASS_DEATH_KNIGHT = 'Death Knight';
    const CLASS_DRUID        = 'Druid';
    const CLASS_HUNTER       = 'Hunter';
    const CLASS_MAGE         = 'Mage';
    const CLASS_PALADIN      = 'Paladin';
    const CLASS_PRIEST       = 'Priest';
    const CLASS_ROGUE        = 'Rogue';
    const CLASS_SHAMAN       = 'Shaman';
    const CLASS_WARLOCK      = 'Warlock';
    const CLASS_WARRIOR      = 'Warrior';

    const PROFESSION_ALCHEMY        = 'Alchemy';
    const PROFESSION_BLACKSMITHING  = 'Blacksmithing';
    const PROFESSION_ENCHANTING     = 'Enchanting';
    const PROFESSION_ENGINEERING    = 'Engineering';
    const PROFESSION_HERBALISM      = 'Herbalism';
    const PROFESSION_INSCRIPTION    = 'Inscription';
    const PROFESSION_JEWELCRAFTING  = 'Jewelcrafting';
    const PROFESSION_LEATHERWORKING = 'Leatherworking';
    const PROFESSION_MINING         = 'Mining';
    const PROFESSION_SKINNING       = 'Skinning';
    const PROFESSION_TAILORING      = 'Tailoring';

    public function guild() {
        return $this->belongsTo(Guild::class);
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function raidGroup() {
        return $this->belongsTo(RaidGroup::class);
    }

    public function secondaryRaidGroups() {
        return $this->belongsToMany(RaidGroup::class, 'character_raid_groups', 'character_id', 'raid_group_id');
    }

    public function raids() {
        return $this->belongsToMany(Raid::class, 'raid_characters', 'character_id', 'raid_id')
            ->orderByDesc('raids.date')
            ->withPivot([
                'is_exempt',
                'credit',
                'remark_id',
                'public_note',
                'officer_note',
            ])
            ->withTimeStamps();
    }

    public function recipes() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->select(['items.*', 'added_by_members.username AS added_by_username', 'instances.id AS instance_id'])
            ->leftJoin('members AS added_by_members', 'added_by_members.id', '=', 'character_items.added_by')
            ->leftJoin('item_item_sources',           'items.item_id',                    '=', 'item_item_sources.item_id')
            ->leftJoin('item_sources',                'item_item_sources.item_source_id', '=', 'item_sources.id')
            ->leftJoin('instances',                   'item_sources.instance_id',         '=', 'instances.id')
            ->where('character_items.type', Item::TYPE_RECIPE)
            ->groupBy('character_items.id')
            ->orderBy('character_items.order')
            ->withPivot([
                'id',
                'added_by',
                'type',
                'order',
                'raid_group_id',
                'created_at',
            ])
            ->withTimeStamps();

        return ($query);
    }

    public function received() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->select([
                'items.*',
                'added_by_members.username AS added_by_username',
                'raid_groups.name          AS raid_group_name',
                'instances.id              AS instance_id',
                'guild_items.tier          AS guild_tier',
            ])
            ->join(    'characters',                  'characters.id',                    '=', 'character_items.character_id')
            ->leftJoin('members AS added_by_members', 'added_by_members.id',              '=', 'character_items.added_by')
            ->leftJoin('raid_groups',                 'raid_groups.id',                   '=', 'character_items.raid_group_id')
            ->leftJoin('item_item_sources',           'items.item_id',                    '=', 'item_item_sources.item_id')
            ->leftJoin('item_sources',                'item_item_sources.item_source_id', '=', 'item_sources.id')
            ->leftJoin('instances',                   'item_sources.instance_id',         '=', 'instances.id')
            ->leftJoin('guild_items', function ($join) {
                $join->on('guild_items.item_id', 'items.item_id')
                    ->on('guild_items.guild_id', 'characters.guild_id'); // I spent too long before googling why `where()` wasn't working: https://stackoverflow.com/a/29544890/1196517
            })
            ->where('character_items.type', Item::TYPE_RECEIVED)
            ->groupBy('character_items.id')
            // Composite order by which checks for received_at date and uses that first, and then created_at date as a fallback
            // Sorts by `order` first though
            ->orderByRaw('`character_items`.`order`, IF(`character_items`.`received_at`, `character_items`.`received_at`, `character_items`.`created_at`) DESC')
            ->withPivot([
                'id',
                'added_by',
                'type',
                'order',
                'note',
                'officer_note',
                'is_offspec',
                'raid_group_id',
                'received_at',
                'created_at'
            ])
            ->withTimeStamps();

        return ($query);
    }

    public function prios() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->select([
                'items.*',
                'added_by_members.username AS added_by_username',
                'instances.id              AS instance_id',
                'guild_items.tier          AS guild_tier',
            ])
            ->join(    'characters',                  'characters.id',                    '=', 'character_items.character_id')
            ->leftJoin('members AS added_by_members', 'added_by_members.id',              '=', 'character_items.added_by')
            ->leftJoin('raid_groups',                 'character_items.raid_group_id',    '=', 'raid_groups.id')
            ->leftJoin('item_item_sources',           'items.item_id',                    '=', 'item_item_sources.item_id')
            ->leftJoin('item_sources',                'item_item_sources.item_source_id', '=', 'item_sources.id')
            ->leftJoin('instances',                   'item_sources.instance_id',         '=', 'instances.id')
            ->leftJoin('guild_items', function ($join) {
                $join->on('guild_items.item_id', 'items.item_id')
                    ->on('guild_items.guild_id', 'characters.guild_id');
            })
            ->where([
                ['character_items.type', Item::TYPE_PRIO],
            ])
            ->whereNull('raid_groups.disabled_at')
            ->orderBy('character_items.raid_group_id')
            ->orderBy('character_items.order')
            ->groupBy('character_items.id')
            ->withPivot([
                'id',
                'added_by',
                'type',
                'order',
                'is_received',
                'received_at',
                'raid_group_id',
                'created_at',
            ])
            ->withTimeStamps();

        return ($query);
    }

    public function wishlist() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->select([
                'items.*',
                'item_sources.id           AS item_source_id',
                'item_sources.instance_id  AS instance_id',
                'instances.name            AS instance_name',
                'instances.order           AS instance_order',
                'added_by_members.username AS added_by_username',
                'guild_items.tier          AS guild_tier',
            ])
            ->join(    'characters',                  'characters.id',                    '=', 'character_items.character_id')
            ->leftJoin('members AS added_by_members', 'added_by_members.id',              '=', 'character_items.added_by')
            ->leftJoin('item_item_sources',           'items.item_id',                    '=', 'item_item_sources.item_id')
            ->leftJoin('item_sources',                'item_item_sources.item_source_id', '=', 'item_sources.id')
            ->leftJoin('instances',                   'item_sources.instance_id',         '=', 'instances.id')
            ->leftJoin('guild_items', function ($join) {
                $join->on('guild_items.item_id', 'items.item_id')
                    ->on('guild_items.guild_id', 'characters.guild_id');
            })
            ->where('character_items.type', Item::TYPE_WISHLIST)
            ->groupBy('character_items.id')
            ->orderBy('character_items.order')
            ->withPivot([
                'id',
                'added_by',
                'type',
                'order',
                'is_offspec',
                'is_received',
                'received_at',
                'raid_group_id',
                'created_at',
            ])
            ->withTimeStamps();

        return $query;
    }

    // Takes a query for characters and applies the logic necessary to fetch attendance for those characters.
    // Applies the fields `raid_count` and `attendance_percentage` to the selected fields.
    // Might not work on all queries.
    static public function addAttendanceQuery($query) {
        return $query
            ->addSelect([
                DB::raw('COALESCE(COUNT(`raid_characters`.`id`), 0) AS `raid_count`'),
                DB::raw('IF(COUNT(`raid_characters`.`id`), COALESCE(ROUND(SUM(`raid_characters`.`credit`) / COUNT(`raid_characters`.`id`), 3), 0), 0) AS `attendance_percentage`'),
                // DB::raw('COALESCE(MAX(`raid_characters`.`raid_count`), 0) AS `raid_count`'),
                // DB::raw('COALESCE(ROUND(MAX(`raid_characters`.`credit`) / MAX(`raid_characters`.`raid_count`), 3), 0) AS `attendance_percentage`'),
            ])
            ->join('guilds', 'guilds.id', 'characters.guild_id')
            ->leftJoin('raids', function ($join) {
                $join->on('raids.guild_id', 'characters.guild_id')
                    ->whereRaw('`raids`.`date` BETWEEN (NOW() - INTERVAL `guilds`.`attendance_decay_days` DAY) AND (NOW() - INTERVAL ' . env('ATTENDANCE_DELAY_HOURS', 2) . ' HOUR)')
                    ->whereNull('raids.cancelled_at');
            })
            ->leftJoin('raid_characters', function ($join) {
                $join->on('raid_characters.raid_id', 'raids.id')
                    ->on('raid_characters.character_id', 'characters.id')
                    ->where('raid_characters.is_exempt', 0);
            })
            ->groupBy('characters.id');
    }

    static public function classes($expansionId) {
        switch ($expansionId) {
            case 1: // Classic
                return [
                    self::CLASS_DRUID,
                    self::CLASS_HUNTER,
                    self::CLASS_MAGE,
                    self::CLASS_PALADIN,
                    self::CLASS_PRIEST,
                    self::CLASS_ROGUE,
                    self::CLASS_SHAMAN,
                    self::CLASS_WARLOCK,
                    self::CLASS_WARRIOR,
                ];
                break;
            case 2: // TBC
                return [
                    self::CLASS_DRUID,
                    self::CLASS_HUNTER,
                    self::CLASS_MAGE,
                    self::CLASS_PALADIN,
                    self::CLASS_PRIEST,
                    self::CLASS_ROGUE,
                    self::CLASS_SHAMAN,
                    self::CLASS_WARLOCK,
                    self::CLASS_WARRIOR,
                ];
                break;
            case 3: // WoTLK
                return [
                    self::CLASS_DEATH_KNIGHT,
                    self::CLASS_DRUID,
                    self::CLASS_HUNTER,
                    self::CLASS_MAGE,
                    self::CLASS_PALADIN,
                    self::CLASS_PRIEST,
                    self::CLASS_ROGUE,
                    self::CLASS_SHAMAN,
                    self::CLASS_WARLOCK,
                    self::CLASS_WARRIOR,
                ];
                break;
            default:
                return [];
                break;
        }
    }

    static public function professions($expansionId) {
        switch ($expansionId) {
            case 1: // Classic
                return [
                    self::PROFESSION_ALCHEMY,
                    self::PROFESSION_BLACKSMITHING,
                    self::PROFESSION_ENCHANTING,
                    self::PROFESSION_ENGINEERING,
                    self::PROFESSION_HERBALISM,
                    self::PROFESSION_LEATHERWORKING,
                    self::PROFESSION_MINING,
                    self::PROFESSION_SKINNING,
                    self::PROFESSION_TAILORING,
                ];
                break;
            case 2: // TBC
                return [
                    self::PROFESSION_ALCHEMY,
                    self::PROFESSION_BLACKSMITHING,
                    self::PROFESSION_ENCHANTING,
                    self::PROFESSION_ENGINEERING,
                    self::PROFESSION_HERBALISM,
                    self::PROFESSION_JEWELCRAFTING,
                    self::PROFESSION_LEATHERWORKING,
                    self::PROFESSION_MINING,
                    self::PROFESSION_SKINNING,
                    self::PROFESSION_TAILORING,
                ];
                break;
            case 3: // WoTLK
                return [
                    self::PROFESSION_ALCHEMY,
                    self::PROFESSION_BLACKSMITHING,
                    self::PROFESSION_ENCHANTING,
                    self::PROFESSION_ENGINEERING,
                    self::PROFESSION_HERBALISM,
                    self::PROFESSION_INSCRIPTION,
                    self::PROFESSION_JEWELCRAFTING,
                    self::PROFESSION_LEATHERWORKING,
                    self::PROFESSION_MINING,
                    self::PROFESSION_SKINNING,
                    self::PROFESSION_TAILORING,
                ];
                break;
            default:
                return [];
                break;
        }
    }

    static public function races($expansionId) {
        switch ($expansionId) {
            case 1: // Classic
                return [
                    self::RACE_ORC,
                    self::RACE_TAUREN,
                    self::RACE_TROLL,
                    self::RACE_UNDEAD,
                    self::RACE_DWARF,
                    self::RACE_GNOME,
                    self::RACE_HUMAN,
                    self::RACE_NIGHT_ELF,
                ];
                break;
            case 2: // TBC
                return [
                    self::RACE_BLOOD_ELF,
                    self::RACE_ORC,
                    self::RACE_TAUREN,
                    self::RACE_TROLL,
                    self::RACE_UNDEAD,
                    self::RACE_DRAENEI,
                    self::RACE_DWARF,
                    self::RACE_GNOME,
                    self::RACE_HUMAN,
                    self::RACE_NIGHT_ELF,
                ];
                break;
            case 3: // WoTLK
                return [
                    self::RACE_BLOOD_ELF,
                    self::RACE_ORC,
                    self::RACE_TAUREN,
                    self::RACE_TROLL,
                    self::RACE_UNDEAD,
                    self::RACE_DRAENEI,
                    self::RACE_DWARF,
                    self::RACE_GNOME,
                    self::RACE_HUMAN,
                    self::RACE_NIGHT_ELF,
                ];
                break;
            default:
                return [];
                break;
        }
    }
}
