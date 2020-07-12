<?php

namespace App;

use App\{
    Content,
    Member,
    Raid,
    User,
};

class Guild extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
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

    // Includes banned members
    public function allMembers() {
        return $this->members()->orderBy('username');
    }

    public function content() {
        return $this->hasMany(Content::class)->whereNull('removed_at')->orderByDesc('created_at');
    }

    // Excludes banned members
    public function members() {
        return $this->belongsToMany(Member::class)->whereNull('banned_at')->orderBy('username');
    }

    public function raids() {
        return $this->belongsToMany(Raid::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
