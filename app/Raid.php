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
