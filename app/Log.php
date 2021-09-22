<?php

namespace App;

use App\{BaseModel, Raid};

class Log extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'raid_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function raids()
    {
        return $this->hasMany(Raid::class)->orderByDesc('date');
    }
}
