<?php

namespace App;

use App\{Raid, Role};
use Illuminate\Database\Eloquent\Model;

class RaidGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'role_id',
        'guild_id',
        'disabled_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function characters() {
        return $this->hasMany(Character::class);
    }

    public function priodCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'raid_group_id', 'character_id')
            ->where(['character_items.type' => self::TYPE_PRIO])
            ->select(['characters.*', 'raid_groups.name AS raid_group_name', 'raid_group_roles.color AS raid_group_color', 'added_by_members.username AS added_by_username'])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raid_groups', function ($join) {
                $join->on('raid_groups.id', 'character_items.raid_group_id');
            })
            ->leftJoin('roles AS raid_group_roles', function ($join) {
                $join->on('raid_group_roles.id', 'raid_groups.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->withTimeStamps()
            ->withPivot(['added_by', 'raid_group_id', 'type'])
            ->orderBy('characters.name');
    }

    public function raids()
    {
        return $this->hasMany(Raid::class)->orderByDesc('date');
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function secondaryCharacters() {
        return $this->belongsToMany(Character::class, 'character_raid_groups', 'raid_group_id', 'character_id');
    }

    /**
     * @return string A hex value
     */
    public function getColor() {
        $color = null;

        if ($this->role_id && $this->role->color) {
            $color = $this->role->color;
        }

        return getHexColorFromDec($color);
    }
}
