<?php

namespace App;

use App\{
    Character,
    Content,
    Item,
    Member,
    Raid,
    Role,
    User,
};
use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'user_id',
        'discord_id',
        'admin_role_id',
        'gm_role_id',
        'officer_role_id',
        'raid_leader_role_id',
        'class_leader_role_id',
        'member_role_ids',
        'calendar_link',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'discord_id',
        'admin_role_id',
        'gm_role_id',
        'officer_role_id',
        'raid_leader_role_id',
        'class_leader_role_id',
        'member_role_ids',
        'calendar_link',
    ];

    // Excludes hidden and removed characters
    public function allCharacters() {
        return $this->hasMany(Character::class)->orderBy('name');
    }

    // Includes banned members
    public function allMembers() {
        return $this->members()->orderBy('username');
    }

    public function allRaids() {
        return $this->hasMany(Raid::class);
    }

    // Excludes hidden and removed characters
    public function characters() {
        return $this->hasMany(Character::class)->whereNull('inactive_at')->orderBy('name');
    }

    public function content() {
        return $this->hasMany(Content::class)->whereNull('removed_at')->orderByDesc('created_at');
    }

    public function items() {
        return $this->belongsToMany(Item::class, 'guild_items', 'guild_id', 'item_id')
            ->withTimeStamps()
            ->withPivot(['created_by', 'updated_by', 'note', 'priority'])
            ->orderBy('items.name');
    }

    // Excludes banned members
    public function members() {
        return $this->hasMany(Member::class)->whereNull('banned_at')->orderBy('username');
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function raids() {
        return $this->hasMany(Raid::class)->whereNull('disabled_at');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getMemberRoleIds() {
        return explode(',', $this->member_role_ids);
    }
}
