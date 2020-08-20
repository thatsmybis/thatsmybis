<?php

namespace App;

use App\{
    Raid,
    Role,
};
use Illuminate\Database\Eloquent\Model;

class Raid extends Model
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

    public function priodCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'raid_id', 'character_id')
            ->where(['character_items.type' => self::TYPE_PRIO])
            ->select(['characters.*', 'raids.name AS raid_name', 'raid_roles.color AS raid_color', 'added_by_members.username AS added_by_username'])
            ->whereNull('characters.inactive_at')
            ->leftJoin('raids', function ($join) {
                $join->on('raids.id', 'character_items.raid_id');
            })
            ->leftJoin('roles AS raid_roles', function ($join) {
                $join->on('raid_roles.id', 'raids.role_id');
            })
            ->leftJoin('members AS added_by_members', function ($join) {
                $join->on('added_by_members.id', 'character_items.added_by');
            })
            ->withTimeStamps()
            ->withPivot(['added_by', 'raid_id', 'type'])
            ->orderBy('characters.name');
    }

    public function role() {
        return $this->belongsTo(Role::class);
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
