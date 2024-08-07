<?php

namespace App;

use App\{BaseModel, Batch, Character, Guild, Instance, Item, Member, RaidGroup};

class Raid extends BaseModel
{
    const REMARK_LATE            = 'Late';
    const REMARK_UNPREPARED      = 'Unprepared';
    const REMARK_LATE_UNPREPARED = 'Late & unprepared';
    const REMARK_NO_SHOW         = 'No call, no show';
    const REMARK_AWAY            = 'Gave notice';
    const REMARK_BENCHED         = 'Benched';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'guild_id',
        'member_id',
        'date',
        'ignore_attendance',
        'cancelled_at',
        'archived_at',
        'public_note',
        'officer_note',
        'logs_deprecated',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function batches() {
        return $this->hasMany(Batch::class)->orderBy('batches.name');
    }

    public function characters() {
        return $this->belongsToMany(Character::class, 'raid_characters', 'raid_id', 'character_id')
            ->orderBy('name')
            ->withTimeStamps()
            ->withPivot(['is_exempt', 'credit', 'remark_id', 'public_note', 'officer_note']);
    }

    public function guild() {
        return $this->belongsTo(Guild::class);
    }

    public function instances() {
        return $this->belongsToMany(Instance::class, 'raid_instances', 'raid_id', 'instance_id')->orderBy('order');
    }

    public function items() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'raid_id', 'item_id')
            ->select([
                'items.id',
                'items.item_id',
                'items.parent_id',
                'items.parent_item_id',
                'items.expansion_id',
                'items.name',
                'items.weight',
                'items.quality',
                'items.is_heroic',
                'items.inventory_type',
                'characters.id             AS character_id',
                'characters.name           AS character_name',
                'characters.class          AS character_class',
                'instances.id              AS instance_id',
                'instances.name            AS instance_name',
                'guild_items.tier          AS guild_tier',
            ])
            ->join(    'characters',                  'characters.id',                    '=', 'character_items.character_id')
            ->leftJoin('item_item_sources',           'items.item_id',                    '=', 'item_item_sources.item_id')
            ->leftJoin('item_sources',                'item_item_sources.item_source_id', '=', 'item_sources.id')
            ->leftJoin('instances',                   'item_sources.instance_id',         '=', 'instances.id')
            ->leftJoin('guild_items', function ($join) {
                $join->on('guild_items.item_id', 'items.item_id')
                    ->on('guild_items.guild_id', 'characters.guild_id');
            })
            ->groupBy('character_items.id')
            // Composite order by which checks for received_at date and uses that first, and then created_at date as a fallback
            // Sorts by `order` first though
            ->orderByRaw('`items`.`name`, `characters`.`name`')
            ->withPivot([
                'id',
                'added_by',
                'type',
                'order',
                'note',
                'officer_note',
                'is_offspec',
                'raid_group_id',
                'received_at',
                'created_at'
            ])
            ->withTimeStamps();

        return ($query);
    }

    public function logs() {
        return $this->hasMany(Log::class)->orderBy('logs.name');
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function raidGroups() {
        return $this->belongsToMany(RaidGroup::class, 'raid_raid_groups', 'raid_id', 'raid_group_id')->orderBy('name');
    }

    static public function remarks() {
        return [
            1 => __('Late'),
            2 => __('Unprepared'),
            3 => __('Late & unprepared'),
            4 => __('No call, no show'),
            5 => __('Gave notice'),
            6 => __('Benched'),
        ];
    }
}
