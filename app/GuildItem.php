<?php

namespace App;

use App\BaseModel;

class GuildItem extends BaseModel
{
    /**
     * @return BelongsTo
     */
    public function item (): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    /**
     * @return BelongsTo
     */
    public function guild (): BelongsTo
    {
        return $this->belongsTo(Guild::class);
    }
}
