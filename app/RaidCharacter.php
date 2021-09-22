<?php

namespace App;

use App\BaseModel;

class RaidCharacter extends BaseModel
{
    protected $table = 'raid_characters';

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
    public function raid(): BelongsTo
    {
        return $this->belongsTo(Raid::class, 'raid_id');
    }
}
