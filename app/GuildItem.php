<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuildItem extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guild (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guild::class);
    }
}
