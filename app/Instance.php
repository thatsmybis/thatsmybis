<?php

namespace App;

use App\{Item, ItemSource};
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'order',
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
