<?php

namespace App;

use App\{Character, Guild};
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

    const TYPE_RECEIVED = 'received';
    const TYPE_RECIPE = 'recipe';
    const TYPE_WISHLIST = 'wishlist';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function characters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->withTimeStamps()
            ->withPivot('type')
            ->orderBy('characters.name');
    }

    public function receivedCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->where(['type' => self::TYPE_RECEIVED])
            ->withTimeStamps()
            ->withPivot('type')
            ->orderBy('characters.name');
    }

    public function wishlistCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->where(['type' => self::TYPE_WISHLIST])
            ->withTimeStamps()
            ->withPivot('type')
            ->orderBy('characters.name');
    }
}
