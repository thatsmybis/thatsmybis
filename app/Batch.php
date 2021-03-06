<?php

namespace App;

use App\{Guild, Member, Raid, RaidGroup, User};
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'batches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'note',
        'type',
        'guild_id',
        'member_id',
        'raid_id',
        'raid_group_id',
        'user_id',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function guild() {
        return $this->belongsTo(Guild::class);
    }

    public function items() {
        return $this->belongsToMany(Item::class, 'character_items', 'batch_id', 'item_id')
            ->withTimeStamps()
            ->select([
                'items.*',
                'characters.id AS character_id',
                'characters.name AS character_name',
                'characters.slug AS character_slug',
                'characters.class AS character_class',
                'characters.is_alt AS character_is_alt',
                'characters.spec AS character_spec',
            ])
            ->leftJoin('characters', 'characters.id', 'character_items.character_id')
            ->withPivot(['character_id', 'note', 'officer_note', 'is_offspec']);
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function raid() {
        return $this->belongsTo(Raid::class);
    }

    public function raidGroup() {
        return $this->belongsTo(RaidGroup::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
