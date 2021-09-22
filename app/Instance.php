<?php

namespace App;

use App\{Item, ItemSource};
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    use Cachable;

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
