<?php

namespace App;

use App\{Instance, Item};
use Illuminate\Database\Eloquent\Model;

class ItemSource extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'instance_id',
        'npc_id',
        'object_id',
        'order',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function instance() {
        return $this->belongsTo(Instance::class);
    }

    public function items() {
        return $this->belongsToMany(Item::class, 'item_item_sources', 'item_source_id', 'item_id');
    }
}
