<?php

namespace App;

use App\{Batch, Character, Guild, Instance, Item, Member, RaidGroup};
use Illuminate\Database\Eloquent\Model;

class Raid extends Model
{
    const REMARK_LATE = 'Late';
    const REMARK_UNPREPARED = 'Unprepared';
    const REMARK_LATE_UNPREPARED = 'Late & unprepared';
    const REMARK_NO_SHOW = 'No show';
    const REMARK_BENCHED = 'Benched';

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
        'is_cancelled',
        'public_note',
        'officer_note',
        'logs',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function batches() {
        return $this->hasMany(Batch::class)->orderBy('id');
    }

    public function characters() {
        return $this->hasMany(Character::class)->orderBy('name');
    }

    public function guild() {
        return $this->belongsTo(Guild::class);
    }

    public function instances() {
        return $this->hasMany(Instance::class)->orderBy('order');
    }

    public function items() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'raid_id')
            ->select([
                'items.*',
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
                    ->on('guild_items.guild_id', 'characters.guild_id'); // I spent too long before googling why `where()` wasn't working: https://stackoverflow.com/a/29544890/1196517
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

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function raidGroups() {
        return $this->hasMany(RaidGroup::class)->orderBy('name');
    }

    static public function remarks() {
        return [
            self::REMARK_LATE,
            self::REMARK_UNPREPARED,
            self::REMARK_LATE_UNPREPARED,
            self::REMARK_NO_SHOW,
            self::REMARK_BENCHED,
        ];
    }
}
