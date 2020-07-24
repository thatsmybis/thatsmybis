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
        return $this->hasMany(Character::class)->orderBy('name');
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

    /**
     * Create a member object, attached to a guild, synced with their Discord roles.
     *
     * @param App\User                         $user          The user to create a member for
     * @param RestCord\Model\Guild\GuildMember $discordMember The Discord Member, along with their roles
     * @param App\Guild                        $guild         The guild to attach the user to.
     */
    public static function create($user, $discordMember, $guild) {
        // Create a member for the user
        $member = self::firstOrCreate(['user_id' => $user->id, 'guild_id' => $guild->id], ['username' => $user->username]);

        // Attach the member's current roles from the guild discord
        if (count($discordMember->roles) > 0) {
            $roles = Role::whereIn('discord_id', $discordMember->roles)->get()->keyBy('id')->keys()->toArray();
            $user->roles()->sync($roles);
        }

        return $member;
    }
}
