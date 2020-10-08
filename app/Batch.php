<?php

namespace App;

use App\{Guild, Member, User};
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'batches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'note',
        'type',
        'guild_id',
        'member_id',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function guild() {
        return $this->belongsTo(Guild::class);
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
