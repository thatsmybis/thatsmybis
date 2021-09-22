<?php

namespace App;

use App\BaseModel;

class RaidInstance extends BaseModel
{
    protected $table = 'raid_instances';

    /**
     * @return BelongsTo
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class, 'instance_id');
    }

    /**
     * @return BelongsTo
     */
    public function raid(): BelongsTo
    {
        return $this->belongsTo(Raid::class, 'raid_id');
    }
}
