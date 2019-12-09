<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $primaryKey = 'item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'name',
        'description',
        'note',
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

    public function recipeUsers() {
        return $this->belongsToMany(User::class, 'user_items', 'item_id', 'user_id')
            ->withTimeStamps()
            ->withPivot('type')
            ->where('type', 'recipe');
    }

    public function receivedUsers() {
        return $this->belongsToMany(User::class, 'user_items', 'item_id', 'user_id')
            ->withTimeStamps()
            ->withPivot('type')
            ->where('type', 'received');
    }

    public function wishlistUsers() {
        return $this->belongsToMany(User::class, 'user_items', 'item_id', 'user_id')
            ->withTimeStamps()
            ->withPivot('type')
            ->where('type', 'wishlist');
    }
}
