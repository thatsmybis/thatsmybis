<?php

namespace App;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class CharacterItem extends BaseModel
{
    protected $cachePrefix = 'items';

    protected $table = 'character_items';

    /**
     * @return BelongsTo
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character_id');
    }

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
