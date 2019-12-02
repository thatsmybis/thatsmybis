<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Raid extends Model
{
    protected $table = 'content';

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
}
