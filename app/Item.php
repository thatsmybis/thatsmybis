<?php

namespace App;

use App\{Character, Guild, ItemSource};
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
            ->whereNull('characters.inactive_at')
            ->withTimeStamps()
            ->withPivot('type')
            ->orderBy('characters.name');
    }

    public function guilds() {
        return $this->belongsToMany(Guild::class, 'guild_items', 'item_id', 'guild_id')
            ->withTimeStamps()
            ->withPivot(['created_by', 'updated_by', 'note', 'priority']);
    }

    public function itemSources() {
        return $this->belongsToMany(ItemSource::class, 'item_item_sources', 'item_id', 'item_source_id');
    }

    public function receivedCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->where(['type' => self::TYPE_RECEIVED])
            ->whereNull('characters.inactive_at')
            ->withTimeStamps()
            ->withPivot('type')
            ->orderBy('characters.name');
    }

    public function receivedAndRecipeCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->whereIn('type', [self::TYPE_RECEIVED, self::TYPE_RECIPE])
            ->whereNull('characters.inactive_at')
            ->withTimeStamps()
            ->withPivot('type')
            ->orderBy('characters.name');
    }

    public function wishlistCharacters() {
        return $this->belongsToMany(Character::class, 'character_items', 'item_id', 'character_id')
            ->where(['type' => self::TYPE_WISHLIST])
            ->whereNull('characters.inactive_at')
            ->withTimeStamps()
            ->withPivot('type')
            ->orderBy('characters.name');
    }
}
