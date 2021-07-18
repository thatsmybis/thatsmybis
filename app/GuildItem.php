<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuildItem extends Model
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