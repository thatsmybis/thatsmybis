<?php

namespace App;

use App\{Item, Member, Raid};
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'name',
        'level',
        'race',
        'class',
        'spec',
        'profession_1',
        'profession_2',
        'rank',
        'rank_goal',
        'raid_id',
        'public_note',
        'officer_note',
        'personal_note',
        'position',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'officer_note',
        'personal_note',
    ];

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function raid() {
        return $this->belongsTo(Raid::class);
    }

    public function recipes() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->where('character_items.type', 'recipe')
            ->orderBy('order')
            ->withTimeStamps();

        return ($query);
    }

    public function received() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->where('character_items.type', 'received')
            ->orderBy('order')
            ->withTimeStamps();

        return ($query);
    }

    public function wishlist() {
        $query = $this
            ->belongsToMany(Item::class, 'character_items', 'character_id', 'item_id')
            ->where('character_items.type', 'wishlist')
            ->orderBy('order')
            ->withTimeStamps();

        return ($query);
    }
}
