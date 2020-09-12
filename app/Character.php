<?php

namespace App;

use App\{Item, Guild, Member, Raid};
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Facades\Log;


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
        'raid_id',
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

    const RACE_ORC       = 'Orc';
    const RACE_TAUREN    = 'Tauren';
    const RACE_TROLL     = 'Troll';
    const RACE_UNDEAD    = 'Undead';
    const RACE_DWARF     = 'Dwarf';
    const RACE_GNOME     = 'Gnome';
    const RACE_HUMAN     = 'Human';
    const RACE_NIGHT_ELF = 'Night Elf';

    const CLASS_DRUID   = 'Druid';
    const CLASS_HUNTER  = 'Hunter';
    const CLASS_MAGE    = 'Mage';
    const CLASS_PALADIN = 'Paladin';
    const CLASS_PRIEST  = 'Priest';
    const CLASS_ROGUE   = 'Rogue';
    const CLASS_SHAMAN  = 'Shaman';
    const CLASS_WARLOCK = 'Warlock';
    const CLASS_WARRIOR = 'Warrior';

    const PROFESSION_ALCHEMY        = 'Alchemy';
    const PROFESSION_BLACKSMITHING  = 'Blacksmithing';
    const PROFESSION_ENCHANTING     = 'Enchanting';
    const PROFESSION_ENGINEERING    = 'Engineering';
    const PROFESSION_HERBALISM      = 'Herbalism';
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

    public function raid() {
        return $this->belongsTo(Raid::class);
    }

    public function recipes() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->select(['items.*', 'added_by_members.username AS added_by_username'])
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->where('character_items.type', Item::TYPE_RECIPE)
            ->orderBy('order')
            ->withPivot(['id', 'added_by', 'type', 'order', 'raid_id', 'created_at'])
            ->withTimeStamps();

        return ($query);
    }

    public function received() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->select(['items.*', 'added_by_members.username AS added_by_username'])
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->where('character_items.type', Item::TYPE_RECEIVED)
            ->orderBy('order')
            ->withPivot(['id', 'added_by', 'type', 'order', 'raid_id', 'created_at'])
            ->withTimeStamps();

        return ($query);
    }

    public function prios() {
        $currentMember = request()->get('currentMember');

        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->select(['items.*', 'added_by_members.username AS added_by_username'])
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->where('character_items.type', Item::TYPE_PRIO)
            ->whereIn('character_items.raid_id', $currentMember->raidsWithViewPermissions())
            ->orderBy('character_items.raid_id')
            ->orderBy('character_items.order')
            ->withPivot(['id', 'added_by', 'type', 'order', 'raid_id', 'created_at'])
            ->withTimeStamps();

        return ($query);
    }

    public function wishlist() {
        // check if self
        $currentMember = request()->get('currentMember');        

        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->select(['items.*', 'added_by_members.username AS added_by_username'])
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->where('character_items.type', Item::TYPE_WISHLIST)
            ->whereIn('character_items.raid_id', $currentMember->raidsWithViewPermissions())
            ->orderBy('order')
            ->withPivot(['id', 'added_by', 'type', 'order', 'raid_id', 'created_at'])
            ->withTimeStamps();

        return ($query);
    }

    static public function classes() {
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
    }

    static public function professions() {
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
    }

    static public function races() {
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
    }
}
