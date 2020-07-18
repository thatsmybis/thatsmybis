<?php

namespace App;

use App\{Character, Content, Guild, Role, User};
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'user_id',
        'guild_id',
        'public_note',
        'officer_note',
        'personal_note',
        'banned_at',
        'quit_at',
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

    public function characters()
    {
        return $this->hasMany(Character::class)->orderBy('order');
    }

    public function content()
    {
        return $this->belongsToMany(Content::class)->whereNull('removed_at');
    }

    public function guild() {
        return $this->belongsTo(Guild::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
