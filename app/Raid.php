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
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function raid() {
        return $this->belongsTo(Raid::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }
}
