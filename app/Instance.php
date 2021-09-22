<?php

namespace App;

use App\{BaseModel, Item, ItemSource};

class Instance extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'short_name',
        'slug',
        'order',
        'expansion_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function itemSources() {
        return $this->hasMany(ItemSource::class)->orderBy('item_sources.order');
    }
}
