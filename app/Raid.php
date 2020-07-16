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

    /**
     * @return string A hex value
     */
    public function getColor() {
        $color = null;

        if ($this->color) {
            $color = dechex($this->color);

            // If it's too short, keep adding prefixed zero's till it's long enough
            while (strlen($color) < 6) {
                $color = '0' . $color;
            }
        } else {
            $color = 'FFF';
        }
        return '#' . $color;
    }
}
