<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class RaidRaidGroup extends BaseModel
{
    protected $table = 'raid_raid_groups';

    /**
     * @return BelongsTo
     */
    public function raid(): BelongsTo
    {
        return $this->belongsTo(Raid::class, 'raid_id');
    }

    /**
     * @return BelongsTo
     */
    public function raidGroup(): BelongsTo
    {
        return $this->belongsTo(RaidGroup::class, 'raid_group_id');
    }
}
