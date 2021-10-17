<?php

namespace App;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class CharacterItem extends BaseModel
{
    protected $cachePrefix = 'items';

    protected $table = 'character_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'character_id',
        'raid_id',
        'raid_group_id',
        'added_by',
        'type',
        'order',
        'note',
        'officer_note',
        'is_offspec',
        'is_received',
        'received_at',
        'list_number',
        'import_id',
        'batch_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

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
