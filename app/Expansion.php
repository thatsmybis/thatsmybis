<?php

namespace App;

use App\{BaseModel, Guild};

class Expansion extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'name_short',
        'name_long',
        'slug',
        'is_enabled',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function guilds() {
        return $this->hasMany(Guild::class);
    }
}
