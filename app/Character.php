<?php

namespace App;

use App\{BaseModel, Item, Guild, Member, Raid, RaidGroup};
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\{HasMany};

class Character extends BaseModel
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
        'archetype',
        'spec',
        'spec_label',
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

    const ARCHETYPE_DPS  = 'DPS';
    const ARCHETYPE_HEAL = 'Heal';
    const ARCHETYPE_TANK = 'Tank';

    const SPEC_DEATH_KNIGHT_BLOOD  = 'Blood';
    const SPEC_DEATH_KNIGHT_FROST  = 'Frost (DK)';
    const SPEC_DEATH_KNIGHT_UNHOLY = 'Unholy';
    const SPEC_DRUID_BALANCE       = 'Balance';
    const SPEC_DRUID_FERAL         = 'Feral';
    const SPEC_DRUID_RESTO         = 'Resto (Druid)';
    const SPEC_HUNTER_BEAST        = 'Beast';
    const SPEC_HUNTER_MARKSMAN     = 'Marksman';
    const SPEC_HUNTER_CUNNING      = 'Cunning';
    const SPEC_HUNTER_FEROCITY     = 'Ferocity';
    const SPEC_HUNTER_TENACTIY     = 'Tenacity';
    const SPEC_HUNTER_SURVIVAL     = 'Survival';
    const SPEC_MAGE_ARCANE         = 'Arcane';
    const SPEC_MAGE_FIRE           = 'Fire';
    const SPEC_MAGE_FROST          = 'Frost';
    const SPEC_PALADIN_COMBAT      = 'Retribution';
    const SPEC_PALADIN_HOLY        = 'Holy (Pally)';
    const SPEC_PALADIN_PROTECTION  = 'Prot (Pally)';
    const SPEC_PRIEST_DISCIPLINE   = 'Discipline';
    const SPEC_PRIEST_HOLY         = 'Holy (Priest)';
    const SPEC_PRIEST_SHADOW       = 'Shadow';
    const SPEC_ROGUE_ASSASSIN      = 'Assassination';
    const SPEC_ROGUE_COMBAT        = 'Combat';
    const SPEC_ROGUE_SUBTLETY      = 'Subtlety';
    const SPEC_SHAMAN_ELEMENTAL    = 'Elemental';
    const SPEC_SHAMAN_ENHANCE      = 'Enhancement';
    const SPEC_SHAMAN_RESTO        = 'Resto (Shammy)';
    const SPEC_WARLOCK_AFFLICTION  = 'Affliction';
    const SPEC_WARLOCK_DESTRO      = 'Destruction';
    const SPEC_WARLOCK_DEMON       = 'Demonology';
    const SPEC_WARRIOR_ARMS        = 'Arms';
    const SPEC_WARRIOR_FURY        = 'Fury';
    const SPEC_WARRIOR_PROT        = 'Prot (War)';

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
            ->select([
                'items.id',
                'items.item_id',
                'items.parent_id',
                'items.parent_item_id',
                'items.expansion_id',
                'items.name',
                'items.weight',
                'items.quality',
                'items.inventory_type',
                'added_by_members.username AS added_by_username',
                'instances.id AS instance_id'
            ])
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
                'items.id',
                'items.item_id',
                'items.parent_id',
                'items.parent_item_id',
                'items.expansion_id',
                'items.name',
                'items.weight',
                'items.quality',
                'items.inventory_type',
                'added_by_members.username AS added_by_username',
                'raid_groups.name          AS raid_group_name',
                'raids.name                AS raid_name',
                'raids.slug                AS raid_slug',
                'instances.id              AS instance_id',
                'guild_items.tier          AS guild_tier',
            ])
            ->join(    'characters',                  'characters.id',                    '=', 'character_items.character_id')
            ->leftJoin('members AS added_by_members', 'added_by_members.id',              '=', 'character_items.added_by')
            ->leftJoin('raid_groups',                 'raid_groups.id',                   '=', 'character_items.raid_group_id')
            ->leftJoin('raids',                       'character_items.raid_id',          '=', 'raids.id')
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
                'raid_id',
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
                'items.id',
                'items.item_id',
                'items.parent_id',
                'items.parent_item_id',
                'items.expansion_id',
                'items.name',
                'items.weight',
                'items.quality',
                'items.inventory_type',
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
                'is_offspec',
                'is_received',
                'received_at',
                'raid_group_id',
                'created_at',
            ])
            ->withTimeStamps();

        return ($query);
    }

    public function allWishlists() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->select([
                'items.id',
                'items.item_id',
                'items.parent_id',
                'items.parent_item_id',
                'items.expansion_id',
                'items.name',
                'items.weight',
                'items.quality',
                'items.inventory_type',
                'item_sources.id             AS item_source_id',
                'item_sources.instance_id    AS instance_id',
                'instances.name              AS instance_name',
                'instances.order             AS instance_order',
                'added_by_members.username   AS added_by_username',
                'guild_items.tier            AS guild_tier',
                'character_items.list_number AS list_number',
            ])
            ->join('characters', 'characters.id', '=', 'character_items.character_id')
            ->join('guilds AS wishlist_guilds',       'wishlist_guilds.id',  '=', 'characters.guild_id')
            ->leftJoin('members AS added_by_members', 'added_by_members.id', '=', 'character_items.added_by')
            ->leftJoin('item_item_sources',           'items.item_id',       '=', 'item_item_sources.item_id')
            ->leftJoin('item_sources',                'item_item_sources.item_source_id', '=', 'item_sources.id')
            ->leftJoin('instances',                   'item_sources.instance_id',         '=', 'instances.id')
            ->leftJoin('guild_items', function ($join) {
                $join->on('guild_items.item_id', 'items.item_id')
                    ->on('guild_items.guild_id', 'characters.guild_id');
            })
            ->where([
                'character_items.type' => Item::TYPE_WISHLIST,
            ])
            ->groupBy('character_items.id')
            ->orderBy('character_items.order')
            ->withPivot([
                'id',
                'added_by',
                'type',
                'order',
                'list_number',
                'is_offspec',
                'is_received',
                'received_at',
                'raid_group_id',
                'raid_id',
                'created_at',
            ])
            ->withTimeStamps();

        return $query;
    }

    public function wishlist() {
        return $this->allWishlists()
            ->where(['character_items.list_number' => DB::raw('`wishlist_guilds`.`current_wishlist_number`')]);
    }

    /**
     * @return HasMany
     */
    public function outstandingItems(): HasMany
    {
        return $this->hasMany(CharacterItem::class)
            ->where('is_received', 0)
            ->whereIn('type', ['wishlist', 'prio']);
    }

    // Takes a query for characters and applies the logic necessary to fetch attendance for those characters.
    // Applies the fields `raid_count` and `attendance_percentage` to the selected fields.
    // Might not work on all queries.
    static public function addAttendanceQuery($query) {
        $query = $query
            ->addSelect([
                DB::raw('COALESCE(COUNT(`raid_characters`.`id`), 0) AS `raid_count`'),
                DB::raw('COALESCE(COUNT(`raid_characters_benched`.`id`), 0) AS `benched_count`'),
                DB::raw('IF(COUNT(`raid_characters`.`id`), COALESCE(ROUND(SUM(`raid_characters`.`credit`) / COUNT(`raid_characters`.`id`), 3), 0), 0) AS `attendance_percentage`'),
                // DB::raw('COALESCE(MAX(`raid_characters`.`raid_count`), 0) AS `raid_count`'),
                // DB::raw('COALESCE(ROUND(MAX(`raid_characters`.`credit`) / MAX(`raid_characters`.`raid_count`), 3), 0) AS `attendance_percentage`'),
            ])
            ->join('guilds', 'guilds.id', 'characters.guild_id');

        $raidGroupId = request()->get('raidGroupIdFilter');
        if ($raidGroupId) {
            $query = $query->leftJoin('raids', function ($join) use ($raidGroupId) {
                $join->on('raids.guild_id', 'characters.guild_id')
                    ->join('raid_raid_groups', function ($join) use ($raidGroupId) {
                        $join->on('raid_raid_groups.raid_id', 'raids.id')
                            ->where('raid_raid_groups.raid_group_id', $raidGroupId);
                    })
                    ->whereRaw('`raids`.`date` BETWEEN (NOW() - INTERVAL `guilds`.`attendance_decay_days` DAY) AND (NOW() - INTERVAL ' . env('ATTENDANCE_DELAY_HOURS', 1) . ' HOUR)')
                    ->whereNull('raids.cancelled_at');
            });
        } else {
            $query = $query->leftJoin('raids', function ($join) {
                $join->on('raids.guild_id', 'characters.guild_id')
                    ->whereRaw('`raids`.`date` BETWEEN (NOW() - INTERVAL `guilds`.`attendance_decay_days` DAY) AND (NOW() - INTERVAL ' . env('ATTENDANCE_DELAY_HOURS', 1) . ' HOUR)')
                    ->whereNull('raids.cancelled_at');
            });
        }

        $query = $query
            ->leftJoin('raid_characters', function ($join) {
                $join->on('raid_characters.raid_id', 'raids.id')
                    ->on('raid_characters.character_id', 'characters.id')
                    ->where('raid_characters.is_exempt', 0);
            })
            ->leftJoin('raid_characters AS raid_characters_benched', function ($join) {
                $join->on('raid_characters_benched.raid_id', 'raids.id')
                    ->on('raid_characters_benched.character_id', 'characters.id')
                    ->where('raid_characters_benched.remark_id', 6); // 6 = benched
            });

        return $query;
    }

    public function getDisplayArchetypeAttribute()
    {
        return $this->archetype ? self::archetypes()[$this->archetype] : null;
    }

    public function getDisplayClassAttribute()
    {
        return self::classes()[$this->class];
    }

    public function getDisplaySpecAttribute()
    {
        return $this->spec_label ? $this->spec_label : ($this->spec ? self::specs()[$this->spec]['name'] : null);
    }

    static public function archetypes() {
        return [
            self::ARCHETYPE_DPS  => __('DPS'),
            self::ARCHETYPE_HEAL => __('Heal'),
            self::ARCHETYPE_TANK => __('Tank'),
        ];
    }


    static public function classes($expansionId = 0) {
        switch ($expansionId) {
            case 1: // Classic
                return [
                    self::CLASS_DRUID   => __('Druid'),
                    self::CLASS_HUNTER  => __('Hunter'),
                    self::CLASS_MAGE    => __('Mage'),
                    self::CLASS_PALADIN => __('Paladin'),
                    self::CLASS_PRIEST  => __('Priest'),
                    self::CLASS_ROGUE   => __('Rogue'),
                    self::CLASS_SHAMAN  => __('Shaman'),
                    self::CLASS_WARLOCK => __('Warlock'),
                    self::CLASS_WARRIOR => __('Warrior'),
                ];
                break;
            case 2: // TBC
                return [
                    self::CLASS_DRUID   => __('Druid'),
                    self::CLASS_HUNTER  => __('Hunter'),
                    self::CLASS_MAGE    => __('Mage'),
                    self::CLASS_PALADIN => __('Paladin'),
                    self::CLASS_PRIEST  => __('Priest'),
                    self::CLASS_ROGUE   => __('Rogue'),
                    self::CLASS_SHAMAN  => __('Shaman'),
                    self::CLASS_WARLOCK => __('Warlock'),
                    self::CLASS_WARRIOR => __('Warrior'),
                ];
                break;
            default: // WoTLK
                return [
                    self::CLASS_DEATH_KNIGHT => __('Death Knight'),
                    self::CLASS_DRUID        => __('Druid'),
                    self::CLASS_HUNTER       => __('Hunter'),
                    self::CLASS_MAGE         => __('Mage'),
                    self::CLASS_PALADIN      => __('Paladin'),
                    self::CLASS_PRIEST       => __('Priest'),
                    self::CLASS_ROGUE        => __('Rogue'),
                    self::CLASS_SHAMAN       => __('Shaman'),
                    self::CLASS_WARLOCK      => __('Warlock'),
                    self::CLASS_WARRIOR      => __('Warrior'),
                ];
                break;
        }
    }

    static public function professions($expansionId = 0) {
        switch ($expansionId) {
            case 1: // Classic
                return [
                    self::PROFESSION_ALCHEMY        => __('Alchemy'),
                    self::PROFESSION_BLACKSMITHING  => __('Blacksmithing'),
                    self::PROFESSION_ENCHANTING     => __('Enchanting'),
                    self::PROFESSION_ENGINEERING    => __('Engineering'),
                    self::PROFESSION_HERBALISM      => __('Herbalism'),
                    self::PROFESSION_LEATHERWORKING => __('Leatherworking'),
                    self::PROFESSION_MINING         => __('Mining'),
                    self::PROFESSION_SKINNING       => __('Skinning'),
                    self::PROFESSION_TAILORING      => __('Tailoring'),
                ];
                break;
            case 2: // TBC
                return [
                    self::PROFESSION_ALCHEMY        => __('Alchemy'),
                    self::PROFESSION_BLACKSMITHING  => __('Blacksmithing'),
                    self::PROFESSION_ENCHANTING     => __('Enchanting'),
                    self::PROFESSION_ENGINEERING    => __('Engineering'),
                    self::PROFESSION_HERBALISM      => __('Herbalism'),
                    self::PROFESSION_JEWELCRAFTING  => __('Jewelcrafting'),
                    self::PROFESSION_LEATHERWORKING => __('Leatherworking'),
                    self::PROFESSION_MINING         => __('Mining'),
                    self::PROFESSION_SKINNING       => __('Skinning'),
                    self::PROFESSION_TAILORING      => __('Tailoring'),
                ];
                break;
            default: // WoTLK
                return [
                    self::PROFESSION_ALCHEMY        => __('Alchemy'),
                    self::PROFESSION_BLACKSMITHING  => __('Blacksmithing'),
                    self::PROFESSION_ENCHANTING     => __('Enchanting'),
                    self::PROFESSION_ENGINEERING    => __('Engineering'),
                    self::PROFESSION_HERBALISM      => __('Herbalism'),
                    self::PROFESSION_INSCRIPTION    => __('Inscription'),
                    self::PROFESSION_JEWELCRAFTING  => __('Jewelcrafting'),
                    self::PROFESSION_LEATHERWORKING => __('Leatherworking'),
                    self::PROFESSION_MINING         => __('Mining'),
                    self::PROFESSION_SKINNING       => __('Skinning'),
                    self::PROFESSION_TAILORING      => __('Tailoring'),
                ];
                break;
        }
    }

    static public function races($expansionId = 0) {
        switch ($expansionId) {
            case 1: // Classic
                return [
                    self::RACE_ORC       => __('Orc'),
                    self::RACE_TAUREN    => __('Tauren'),
                    self::RACE_TROLL     => __('Troll'),
                    self::RACE_UNDEAD    => __('Undead'),
                    self::RACE_DWARF     => __('Dwarf'),
                    self::RACE_GNOME     => __('Gnome'),
                    self::RACE_HUMAN     => __('Human'),
                    self::RACE_NIGHT_ELF => __('Night Elf'),
                ];
                break;
            case 2: // TBC
                return [
                    self::RACE_BLOOD_ELF => __('Blood Elf'),
                    self::RACE_ORC       => __('Orc'),
                    self::RACE_TAUREN    => __('Tauren'),
                    self::RACE_TROLL     => __('Troll'),
                    self::RACE_UNDEAD    => __('Undead'),
                    self::RACE_DRAENEI   => __('Draenei'),
                    self::RACE_DWARF     => __('Dwarf'),
                    self::RACE_GNOME     => __('Gnome'),
                    self::RACE_HUMAN     => __('Human'),
                    self::RACE_NIGHT_ELF => __('Night Elf'),
                ];
                break;
            default: // WoTLK
                return  [
                    self::RACE_BLOOD_ELF => __('Blood Elf'),
                    self::RACE_ORC       => __('Orc'),
                    self::RACE_TAUREN    => __('Tauren'),
                    self::RACE_TROLL     => __('Troll'),
                    self::RACE_UNDEAD    => __('Undead'),
                    self::RACE_DRAENEI   => __('Draenei'),
                    self::RACE_DWARF     => __('Dwarf'),
                    self::RACE_GNOME     => __('Gnome'),
                    self::RACE_HUMAN     => __('Human'),
                    self::RACE_NIGHT_ELF => __('Night Elf'),
                ];
                break;
        }
    }

    static public function specs($expansionId = 0) {
        switch ($expansionId) {
            case 1: // Classic
                return [
                    self::SPEC_DRUID_BALANCE      => ['name' => __('Balance'),       'class' => self::CLASS_DRUID,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_balance.jpg'],
                    self::SPEC_DRUID_FERAL        => ['name' => __('Feral'),         'class' => self::CLASS_DRUID,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_feral.jpg'],
                    self::SPEC_DRUID_RESTO        => ['name' => __('Restoration'),   'class' => self::CLASS_DRUID,   'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_restoration_druid.jpg'],
                    self::SPEC_HUNTER_BEAST       => ['name' => __('Beast'),         'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_beast.jpg'],
                    self::SPEC_HUNTER_MARKSMAN    => ['name' => __('Marksmanship'),  'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_marksmanship.jpg'],
                    self::SPEC_HUNTER_SURVIVAL    => ['name' => __('Survival'),      'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_survival.jpg'],
                    self::SPEC_MAGE_ARCANE        => ['name' => __('Arcane'),        'class' => self::CLASS_MAGE,    'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_arcane.jpg'],
                    self::SPEC_MAGE_FIRE          => ['name' => __('Fire'),          'class' => self::CLASS_MAGE,    'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_fire.jpg'],
                    self::SPEC_MAGE_FROST         => ['name' => __('Frost'),         'class' => self::CLASS_MAGE,    'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_frost_mage.jpg'],
                    self::SPEC_PALADIN_COMBAT     => ['name' => __('Retribution'),   'class' => self::CLASS_PALADIN, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_retribution.jpg'],
                    self::SPEC_PALADIN_HOLY       => ['name' => __('Holy'),          'class' => self::CLASS_PALADIN, 'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_holy.jpg'],
                    self::SPEC_PALADIN_PROTECTION => ['name' => __('Protection'),    'class' => self::CLASS_PALADIN, 'archetype' => self::ARCHETYPE_TANK, 'icon' => 'spec_protection_paladin.jpg'],
                    self::SPEC_PRIEST_DISCIPLINE  => ['name' => __('Discipline'),    'class' => self::CLASS_PRIEST,  'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_discipline.jpg'],
                    self::SPEC_PRIEST_HOLY        => ['name' => __('Holy'),          'class' => self::CLASS_PRIEST,  'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_holy.jpg'],
                    self::SPEC_PRIEST_SHADOW      => ['name' => __('Shadow'),        'class' => self::CLASS_PRIEST,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_shadow.jpg'],
                    self::SPEC_ROGUE_ASSASSIN     => ['name' => __('Assassination'), 'class' => self::CLASS_ROGUE,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_assassination.jpg'],
                    self::SPEC_ROGUE_COMBAT       => ['name' => __('Combat'),        'class' => self::CLASS_ROGUE,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_combat.jpg'],
                    self::SPEC_ROGUE_SUBTLETY     => ['name' => __('Subtlety'),      'class' => self::CLASS_ROGUE,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_subtlety.jpg'],
                    self::SPEC_SHAMAN_ELEMENTAL   => ['name' => __('Elemental'),     'class' => self::CLASS_SHAMAN,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_elemental.jpg'],
                    self::SPEC_SHAMAN_ENHANCE     => ['name' => __('Enhancement'),   'class' => self::CLASS_SHAMAN,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_enhancement.jpg'],
                    self::SPEC_SHAMAN_RESTO       => ['name' => __('Restoration'),   'class' => self::CLASS_SHAMAN,  'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_restoration_shaman.jpg'],
                    self::SPEC_WARLOCK_AFFLICTION => ['name' => __('Affliction'),    'class' => self::CLASS_WARLOCK, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_affliction.jpg'],
                    self::SPEC_WARLOCK_DESTRO     => ['name' => __('Destruction'),   'class' => self::CLASS_WARLOCK, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_destruction.jpg'],
                    self::SPEC_WARLOCK_DEMON      => ['name' => __('Demonology'),    'class' => self::CLASS_WARLOCK, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_demonology.jpg'],
                    self::SPEC_WARRIOR_ARMS       => ['name' => __('Arms'),          'class' => self::CLASS_WARRIOR, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_arms.jpg'],
                    self::SPEC_WARRIOR_FURY       => ['name' => __('Fury'),          'class' => self::CLASS_WARRIOR, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_fury.jpg'],
                    self::SPEC_WARRIOR_PROT       => ['name' => __('Protection'),    'class' => self::CLASS_WARRIOR, 'archetype' => self::ARCHETYPE_TANK, 'icon' => 'spec_protection_warrior.jpg'],
                ];
                break;
            case 2: // TBC
                return [
                    self::SPEC_DRUID_BALANCE       => ['name' => __('Balance'),       'class' => self::CLASS_DRUID,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_balance.jpg'],
                    self::SPEC_DRUID_FERAL         => ['name' => __('Feral'),         'class' => self::CLASS_DRUID,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_feral.jpg'],
                    self::SPEC_DRUID_RESTO         => ['name' => __('Restoration'),   'class' => self::CLASS_DRUID,   'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_restoration_druid.jpg'],
                    self::SPEC_HUNTER_BEAST        => ['name' => __('Beast'),         'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_beast.jpg'],
                    self::SPEC_HUNTER_MARKSMAN     => ['name' => __('Marksmanship'),  'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_marksmanship.jpg'],
                    self::SPEC_HUNTER_SURVIVAL     => ['name' => __('Survival'),      'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_survival.jpg'],
                    self::SPEC_MAGE_ARCANE         => ['name' => __('Arcane'),        'class' => self::CLASS_MAGE,    'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_arcane.jpg'],
                    self::SPEC_MAGE_FIRE           => ['name' => __('Fire'),          'class' => self::CLASS_MAGE,    'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_fire.jpg'],
                    self::SPEC_MAGE_FROST          => ['name' => __('Frost'),         'class' => self::CLASS_MAGE,    'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_frost_mage.jpg'],
                    self::SPEC_PALADIN_COMBAT      => ['name' => __('Retribution'),   'class' => self::CLASS_PALADIN, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_retribution.jpg'],
                    self::SPEC_PALADIN_HOLY        => ['name' => __('Holy'),          'class' => self::CLASS_PALADIN, 'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_holy.jpg'],
                    self::SPEC_PALADIN_PROTECTION  => ['name' => __('Protection'),    'class' => self::CLASS_PALADIN, 'archetype' => self::ARCHETYPE_TANK, 'icon' => 'spec_protection_paladin.jpg'],
                    self::SPEC_PRIEST_DISCIPLINE   => ['name' => __('Discipline'),    'class' => self::CLASS_PRIEST,  'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_discipline.jpg'],
                    self::SPEC_PRIEST_HOLY         => ['name' => __('Holy'),          'class' => self::CLASS_PRIEST,  'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_holy.jpg'],
                    self::SPEC_PRIEST_SHADOW       => ['name' => __('Shadow'),        'class' => self::CLASS_PRIEST,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_shadow.jpg'],
                    self::SPEC_ROGUE_ASSASSIN      => ['name' => __('Assassination'), 'class' => self::CLASS_ROGUE,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_assassination.jpg'],
                    self::SPEC_ROGUE_COMBAT        => ['name' => __('Combat'),        'class' => self::CLASS_ROGUE,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_combat.jpg'],
                    self::SPEC_ROGUE_SUBTLETY      => ['name' => __('Subtlety'),      'class' => self::CLASS_ROGUE,   'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_subtlety.jpg'],
                    self::SPEC_SHAMAN_ELEMENTAL    => ['name' => __('Elemental'),     'class' => self::CLASS_SHAMAN,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_elemental.jpg'],
                    self::SPEC_SHAMAN_ENHANCE      => ['name' => __('Enhancement'),   'class' => self::CLASS_SHAMAN,  'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_enhancement.jpg'],
                    self::SPEC_SHAMAN_RESTO        => ['name' => __('Restoration'),   'class' => self::CLASS_SHAMAN,  'archetype' => self::ARCHETYPE_HEAL, 'icon' => 'spec_restoration_shaman.jpg'],
                    self::SPEC_WARLOCK_AFFLICTION  => ['name' => __('Affliction'),    'class' => self::CLASS_WARLOCK, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_affliction.jpg'],
                    self::SPEC_WARLOCK_DESTRO      => ['name' => __('Destruction'),   'class' => self::CLASS_WARLOCK, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_destruction.jpg'],
                    self::SPEC_WARLOCK_DEMON       => ['name' => __('Demonology'),    'class' => self::CLASS_WARLOCK, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_demonology.jpg'],
                    self::SPEC_WARRIOR_ARMS        => ['name' => __('Arms'),          'class' => self::CLASS_WARRIOR, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_arms.jpg'],
                    self::SPEC_WARRIOR_FURY        => ['name' => __('Fury'),          'class' => self::CLASS_WARRIOR, 'archetype' => self::ARCHETYPE_DPS,  'icon' => 'spec_fury.jpg'],
                    self::SPEC_WARRIOR_PROT        => ['name' => __('Protection'),    'class' => self::CLASS_WARRIOR, 'archetype' => self::ARCHETYPE_TANK, 'icon' => 'spec_protection_warrior.jpg'],
                ];
                break;
            default: // WoTLK
                return [
                    self::SPEC_DEATH_KNIGHT_BLOOD  => ['name' => __('Blood'),         'class' => self::CLASS_DEATH_KNIGHT, 'archetype' => self::ARCHETYPE_DPS, 'icon' => 'spec_blood.jpg'],
                    self::SPEC_DEATH_KNIGHT_FROST  => ['name' => __('Frost'),         'class' => self::CLASS_DEATH_KNIGHT, 'archetype' => self::ARCHETYPE_DPS, 'icon' => 'spec_frost_dk.jpg'],
                    self::SPEC_DEATH_KNIGHT_UNHOLY => ['name' => __('Unholy'),        'class' => self::CLASS_DEATH_KNIGHT, 'archetype' => self::ARCHETYPE_DPS, 'icon' => 'spec_unholy.jpg'],
                    self::SPEC_DRUID_BALANCE       => ['name' => __('Balance'),       'class' => self::CLASS_DRUID,   'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_balance.jpg'],
                    self::SPEC_DRUID_FERAL         => ['name' => __('Feral'),         'class' => self::CLASS_DRUID,   'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_feral.jpg'],
                    self::SPEC_DRUID_RESTO         => ['name' => __('Restoration'),   'class' => self::CLASS_DRUID,   'archetype' => self::ARCHETYPE_HEAL,     'icon' => 'spec_restoration_druid.jpg'],
                    self::SPEC_HUNTER_BEAST        => ['name' => __('Beast'),         'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_beast.jpg'],
                    self::SPEC_HUNTER_MARKSMAN     => ['name' => __('Marksmanship'),  'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_marksmanship.jpg'],
                    self::SPEC_HUNTER_SURVIVAL     => ['name' => __('Survival'),      'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_survival.jpg'],
                    self::SPEC_MAGE_ARCANE         => ['name' => __('Arcane'),        'class' => self::CLASS_MAGE,    'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_arcane.jpg'],
                    self::SPEC_MAGE_FIRE           => ['name' => __('Fire'),          'class' => self::CLASS_MAGE,    'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_fire.jpg'],
                    self::SPEC_MAGE_FROST          => ['name' => __('Frost'),         'class' => self::CLASS_MAGE,    'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_frost_mage.jpg'],
                    self::SPEC_PALADIN_COMBAT      => ['name' => __('Retribution'),   'class' => self::CLASS_PALADIN, 'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_retribution.jpg'],
                    self::SPEC_PALADIN_HOLY        => ['name' => __('Holy'),          'class' => self::CLASS_PALADIN, 'archetype' => self::ARCHETYPE_HEAL,     'icon' => 'spec_holy.jpg'],
                    self::SPEC_PALADIN_PROTECTION  => ['name' => __('Protection'),    'class' => self::CLASS_PALADIN, 'archetype' => self::ARCHETYPE_TANK,     'icon' => 'spec_protection_paladin.jpg'],
                    self::SPEC_PRIEST_DISCIPLINE   => ['name' => __('Discipline'),    'class' => self::CLASS_PRIEST,  'archetype' => self::ARCHETYPE_HEAL,     'icon' => 'spec_discipline.jpg'],
                    self::SPEC_PRIEST_HOLY         => ['name' => __('Holy'),          'class' => self::CLASS_PRIEST,  'archetype' => self::ARCHETYPE_HEAL,     'icon' => 'spec_holyjpgg'],
                    self::SPEC_PRIEST_SHADOW       => ['name' => __('Shadow'),        'class' => self::CLASS_PRIEST,  'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_shadow.jpg'],
                    self::SPEC_ROGUE_ASSASSIN      => ['name' => __('Assassination'), 'class' => self::CLASS_ROGUE,   'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_assassination.jpg'],
                    self::SPEC_ROGUE_COMBAT        => ['name' => __('Combat'),        'class' => self::CLASS_ROGUE,   'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_combat.jpg'],
                    self::SPEC_ROGUE_SUBTLETY      => ['name' => __('Subtlety'),      'class' => self::CLASS_ROGUE,   'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_subtlety.jpg'],
                    self::SPEC_SHAMAN_ELEMENTAL    => ['name' => __('Elemental'),     'class' => self::CLASS_SHAMAN,  'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_elemental.jpg'],
                    self::SPEC_SHAMAN_ENHANCE      => ['name' => __('Enhancement'),   'class' => self::CLASS_SHAMAN,  'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_enhancement.jpg'],
                    self::SPEC_SHAMAN_RESTO        => ['name' => __('Restoration'),   'class' => self::CLASS_SHAMAN,  'archetype' => self::ARCHETYPE_HEAL,     'icon' => 'spec_restoration_shaman.jpg'],
                    self::SPEC_WARLOCK_AFFLICTION  => ['name' => __('Affliction'),    'class' => self::CLASS_WARLOCK, 'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_affliction.jpg'],
                    self::SPEC_WARLOCK_DESTRO      => ['name' => __('Destruction'),   'class' => self::CLASS_WARLOCK, 'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_destruction.jpg'],
                    self::SPEC_WARLOCK_DEMON       => ['name' => __('Demonology'),    'class' => self::CLASS_WARLOCK, 'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_demonology.jpg'],
                    self::SPEC_WARRIOR_ARMS        => ['name' => __('Arms'),          'class' => self::CLASS_WARRIOR, 'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_arms.jpg'],
                    self::SPEC_WARRIOR_FURY        => ['name' => __('Fury'),          'class' => self::CLASS_WARRIOR, 'archetype' => self::ARCHETYPE_DPS,      'icon' => 'spec_fury.jpg'],
                    self::SPEC_WARRIOR_PROT        => ['name' => __('Protection'),    'class' => self::CLASS_WARRIOR, 'archetype' => self::ARCHETYPE_TANK,     'icon' => 'spec_protection_warrior.jpg'],
                    // self::SPEC_HUNTER_CUNNING      => ['name' => __('Cunning'),       'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,      'icon' => 'ability_hunter_beasttaming.jpg'],
                    // self::SPEC_HUNTER_FEROCITY     => ['name' => __('Ferocity'),      'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,      'icon' => 'ability_hunter_beasttaming.jpg'],
                    // self::SPEC_HUNTER_TENACTIY     => ['name' => __('Tenacity'),      'class' => self::CLASS_HUNTER,  'archetype' => self::ARCHETYPE_DPS,      'icon' => 'ability_hunter_beasttaming.jpg'],
                ];
                break;
        }
    }
}
