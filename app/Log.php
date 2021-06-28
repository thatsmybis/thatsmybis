<?php

namespace App;

use App\{Raid};
use Illuminate\Database\Eloquent\Model;

class Log extends Model
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
