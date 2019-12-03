<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'name',
        'slot',
        'class',
        'tier',
        'type',
        'source',
        'profession',
        'quality',
        'display_id',
        'inventory_type',
        'allowable_class',
        'item_level',
        'required_level',
        'required_honor_rank',
        'set_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'user_items')->withTimeStamps()->withPivot('type');
    }

    public function wishlistUsers() {
        return $this->belongsToMany(User::class, 'user_items')->withTimeStamps()->withPivot('type')->where('type', 'wishlist');
    }

    public function receivedUsers() {
        return $this->belongsToMany(User::class, 'user_items')->withTimeStamps()->withPivot('type')->where('type', 'received');
    }
}
