<?php

namespace App;

use App\{BaseModel, Character, Guild, Instance, Item, ItemSource, Member, Raid, RaidGroup, Role};

class AuditLog extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'type',
        'character_id',
        'guild_id',
        'batch_id',
        'instance_id',
        'item_id',
        'item_source_id',
        'member_id',
        'other_member_id',
        'raid_id',
        'raid_group_id',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    const TYPE_ASSIGN    = 'assign';
    const TYPE_ITEM_NOTE = 'item_note';

    public function character() {
        return $this->belongsTo(Character::class);
    }

    public function guild() {
        return $this->belongsTo(Guild::class);
    }

    public function instance() {
        return $this->belongsTo(Instance::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function itemSource() {
        return $this->belongsTo(ItemSource::class);
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function raid()
    {
        return $this->belongsTo(Raid::class);
    }

    public function raidGroup() {
        return $this->belongsTo(RaidGroup::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }
}
