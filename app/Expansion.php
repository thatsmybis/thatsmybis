<?php

namespace App;

use App\{Guild};
use Illuminate\Database\Eloquent\Model;

class Expansion extends Model
{
    public const CLASSIC = 1;
    public const TBC = 2;
    public const WOTLK = 3;

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
