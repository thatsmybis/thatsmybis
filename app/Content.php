<?php

namespace App;

use App\{Guild, Member};
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'content';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'category',
        'content',
        'member_id',
        'raid_group_id',
        'guild_id',
        'last_edited_by',
        'removed_at',
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
}
