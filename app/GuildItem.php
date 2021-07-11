<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuildItem extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item () {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guild () {
        return $this->belongsTo(Guild::class);
    }
}
