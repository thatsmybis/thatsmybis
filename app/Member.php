<?php

namespace App;

use App\{Character, Content, Guild, Role, User};
use Kodeine\Acl\Traits\HasRole;

class Member
{
    use HasRole;

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
        return $this->belongsToMany(Character::class, 'member_characters')->orderBy('position');
    }

    public function content()
    {
        return $this->belongsToMany(Content::class)->whereNull('removed_at');
    }

    public function guild() {
        return $this->belongsTo(Guild::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_member')->orderByDesc('position', 'desc');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    // TODO
    // // Fetch the user's roles from Discord and sync them
    // public function fetchAndSyncRoles() {
    //     $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);
    //     $discordMember = $discord->guild->getGuildMember(['guild.id' => (int)env('GUILD_ID'), 'user.id' => (int)$this->discord_id]);
    //     $roles = Role::whereIn('discord_id', $discordMember->roles)->get()->keyBy('id')->keys()->toArray();
    //     $this->syncRoles($roles);
    // }
}
