<?php

namespace App;

use App\{BaseModel, Character, Expansion, Guild, ItemSource};
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends BaseModel
{
    protected $primaryKey = 'item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'expansion_id',
        'parent_id',
        'parent_item_id',
        'name',
        'source',
        'profession',
        'quality',
        'display_id',
        'inventory_type',
        'allowable_class',
        'item_level',
        'required_level',
        'set_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'inventory_type',
        'allowable_class',
        'item_level',
        'name_cn',
        'name_de',
        'name_es',
        'name_fr',
        'name_it',
        'name_ko',
        'name_pt',
        'name_ru',
        'required_level',
        'set_id',
    ];

    const TYPE_PRIO     = 'prio';
    const TYPE_RECEIVED = 'received';
    const TYPE_RECIPE   = 'recipe';
    const TYPE_WISHLIST = 'wishlist';

    public function childItems() {
        return $this->hasMany(Item::class, 'parent_id', 'id')
            ->groupBy('items.item_id') // The same item in multiple expansions could otherwise show up multiple times
            ->orderBy('items.expansion_id')
            ->orderBy('items.name');
    }

    public function characters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->select([
                'characters.*',
                'raid_groups.name AS raid_group_name',
                'raid_group_roles.color AS raid_group_color'])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raid_groups', function ($join) {
                $join->on('raid_groups.id', 'characters.raid_group_id');
            })
            ->leftJoin('roles AS raid_group_roles', function ($join) {
                $join->on('raid_group_roles.id', 'raid_groups.role_id');
            })
            ->withTimeStamps()
            ->withPivot('type')
            ->orderBy('characters.name');
    }

    public function charactersWithAttendance() {
        $query = $this->characters();
        return Character::addAttendanceQuery($query)->groupBy('characters.id');
    }

    public function expansion() {
        return $this->belongsTo(Expansion::class);
    }

    public function guilds() {
        return $this->belongsToMany(Guild::class, 'guild_items', 'item_id', 'guild_id')
            ->withTimeStamps()
            ->withPivot(['created_by', 'updated_by', 'note', 'priority']);
    }

    public function itemSources() {
        return $this->belongsToMany(ItemSource::class, 'item_item_sources', 'item_id', 'item_source_id');
    }

    public function parentItem() {
        return $this->belongsTo(Item::class, 'parent_id', 'id')->orderBy('items.name');
    }

    public function priodCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->where(['character_items.type' => self::TYPE_PRIO])
            ->select([
                'characters.*',
                'raid_groups.name AS raid_group_name',
                'raid_group_roles.color AS raid_group_color',
                DB::raw('MAX(added_by_members.username) AS added_by_username'),
            ])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raid_groups', function ($join) {
                $join->on('raid_groups.id', 'characters.raid_group_id');
            })
            ->leftJoin('roles AS raid_group_roles', function ($join) {
                $join->on('raid_group_roles.id', 'raid_groups.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->withTimeStamps()
            ->withPivot([
                'id',
                'added_by',
                'raid_group_id',
                'type',
                'is_received',
                'is_offspec',
                'received_at',
                'created_at',
                'order',
            ])
            ->orderBy('character_items.raid_group_id')
            ->orderBy('character_items.order')
            ->orderBy('characters.name');
    }

    public function priodCharactersWithAttendance() {
        $query = $this->priodCharacters();
        return Character::addAttendanceQuery($query)->groupBy('characters.id');
    }

    public function receivedCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->where(['character_items.type' => self::TYPE_RECEIVED])
            ->select([
                'characters.*',
                'raid_groups.name AS raid_group_name',
                'raid_group_roles.color AS raid_group_color',
                DB::raw('MAX(added_by_members.username) AS added_by_username'),
            ])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raid_groups', function ($join) {
                $join->on('raid_groups.id', 'characters.raid_group_id');
            })
            ->leftJoin('roles AS raid_group_roles', function ($join) {
                $join->on('raid_group_roles.id', 'raid_groups.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->withTimeStamps()
            ->withPivot([
                'added_by',
                'raid_group_id',
                'type',
                'note',
                'officer_note',
                'is_offspec',
                'received_at',
            ])
            ->orderBy('characters.name');
    }

    public function receivedCharactersWithAttendance() {
        $query = $this->receivedCharacters();
        return Character::addAttendanceQuery($query)->groupBy('characters.id');
    }

    public function receivedAndRecipeCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->whereIn('character_items.type', [self::TYPE_RECEIVED, self::TYPE_RECIPE])
            ->select([
                'characters.*',
                'raid_groups.name AS raid_group_name',
                'raid_group_roles.color AS raid_group_color',
                'added_by_members.username AS added_by_username',
            ])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raid_groups', function ($join) {
                $join->on('raid_groups.id', 'characters.raid_group_id');
            })
            ->leftJoin('roles AS raid_group_roles', function ($join) {
                $join->on('raid_group_roles.id', 'raid_groups.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->withTimeStamps()
            ->withPivot([
                'added_by',
                'raid_group_id',
                'type',
                'order',
                'note',
                'officer_note',
                'is_offspec',
                'received_at',
            ])
            ->orderBy('characters.name');
    }

    public function receivedAndRecipeCharactersWithAttendance() {
        $query = $this->receivedAndRecipeCharacters();
        return Character::addAttendanceQuery($query)->groupBy('characters.id');
    }

    public function wishlistCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->select([
                'characters.*',
                'raid_groups.name AS raid_group_name',
                'raid_group_roles.color AS raid_group_color',
                'added_by_members.username AS added_by_username',
            ])
            ->join('guilds AS wishlist_guilds', 'wishlist_guilds.id', '=', 'characters.guild_id')
            ->leftJoin('raid_groups', function ($join) {
                $join->on('raid_groups.id', 'characters.raid_group_id');
            })
            ->leftJoin('roles AS raid_group_roles', function ($join) {
                $join->on('raid_group_roles.id', 'raid_groups.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->where([
                'character_items.type'        => self::TYPE_WISHLIST,
                // 'character_items.list_number' => DB::raw('wishlist_guilds.current_wishlist_number'),
            ])
            ->whereNull('characters.inactive_at')
            ->withTimeStamps()
            ->withPivot([
                'added_by',
                'type',
                'order',
                'is_received',
                'list_number',
                'is_offspec',
                'received_at',
                'raid_group_id',
                'raid_id',
                'created_at',
            ]);
    }

    public function wishlistCharactersWithAttendance() {
        $query = $this->wishlistCharacters();
        return Character::addAttendanceQuery($query);
    }
}
