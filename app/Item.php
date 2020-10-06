<?php

namespace App;

use App\{Character, Guild, ItemSource};
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $primaryKey = 'item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'note',
        'slot',
        'class',
        'tier',
        'type',
        'source',
        'profession',
        'quality',
        'display_id',
        'inventory_type',
        'allowable_class',
        'item_level',
        'required_level',
        'required_honor_rank',
        'set_id',
    ];

    const TYPE_PRIO     = 'prio';
    const TYPE_RECEIVED = 'received';
    const TYPE_RECIPE   = 'recipe';
    const TYPE_WISHLIST = 'wishlist';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function characters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->select(['characters.*', 'raids.name AS raid_name', 'raid_roles.color AS raid_color'])
            ->whereNull('characters.inactive_at')

            ->leftJoin('raids', function ($join) {
                $join->on('raids.id', 'characters.raid_id');
            })
            ->leftJoin('roles AS raid_roles', function ($join) {
                $join->on('raid_roles.id', 'raids.role_id');
            })
            ->withTimeStamps()
            ->withPivot('type')
            ->orderBy('characters.name');
    }

    public function guilds() {
        return $this->belongsToMany(Guild::class, 'guild_items', 'item_id', 'guild_id')
            ->withTimeStamps()
            ->withPivot(['created_by', 'updated_by', 'note', 'priority']);
    }

    public function itemSources() {
        return $this->belongsToMany(ItemSource::class, 'item_item_sources', 'item_id', 'item_source_id');
    }

    public function priodCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->where(['character_items.type' => self::TYPE_PRIO])
            ->select(['characters.*', 'raids.name AS raid_name', 'raid_roles.color AS raid_color', 'added_by_members.username AS added_by_username'])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raids', function ($join) {
                $join->on('raids.id', 'characters.raid_id');
            })
            ->leftJoin('roles AS raid_roles', function ($join) {
                $join->on('raid_roles.id', 'raids.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->withTimeStamps()
            ->withPivot(['id', 'added_by', 'raid_id', 'type', 'order', 'is_received', 'received_at', 'created_at'])
            ->orderBy('character_items.raid_id')
            ->orderBy('character_items.order');
    }

    public function receivedCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->where(['character_items.type' => self::TYPE_RECEIVED])
            ->select(['characters.*', 'raids.name AS raid_name', 'raid_roles.color AS raid_color', 'added_by_members.username AS added_by_username'])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raids', function ($join) {
                $join->on('raids.id', 'characters.raid_id');
            })
            ->leftJoin('roles AS raid_roles', function ($join) {
                $join->on('raid_roles.id', 'raids.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->withTimeStamps()
            ->withPivot(['added_by', 'raid_id', 'type', 'note', 'officer_note', 'is_offspec', 'received_at'])
            ->orderBy('characters.name');
    }

    public function receivedAndRecipeCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->whereIn('character_items.type', [self::TYPE_RECEIVED, self::TYPE_RECIPE])
            ->select(['characters.*', 'raids.name AS raid_name', 'raid_roles.color AS raid_color', 'added_by_members.username AS added_by_username'])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raids', function ($join) {
                $join->on('raids.id', 'characters.raid_id');
            })
            ->leftJoin('roles AS raid_roles', function ($join) {
                $join->on('raid_roles.id', 'raids.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->withTimeStamps()
            ->withPivot(['added_by', 'raid_id', 'type', 'order', 'note', 'officer_note', 'is_offspec', 'received_at'])
            ->orderBy('characters.name');
    }

    public function wishlistCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->where(['character_items.type' => self::TYPE_WISHLIST])
            ->select(['characters.*', 'raids.name AS raid_name', 'raid_roles.color AS raid_color', 'added_by_members.username AS added_by_username'])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raids', function ($join) {
                $join->on('raids.id', 'characters.raid_id');
            })
            ->leftJoin('roles AS raid_roles', function ($join) {
                $join->on('raid_roles.id', 'raids.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->withTimeStamps()
            ->withPivot(['added_by', 'raid_id', 'type','is_received', 'received_at', 'order'])
            ->orderBy('character_items.order');
    }
}
