<?php

namespace App;

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
        'discord_channel_id',
        'discord_role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function role() {
        return $this->hasOne(Role::class, 'discord_id', 'discord_role_id');
    }
}
