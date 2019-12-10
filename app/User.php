<?php

namespace App;

use App\Role;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kodeine\Acl\Traits\HasRole;
use RestCord\DiscordClient;

class User extends Authenticatable
{
    use Notifiable, HasRole;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        // 'email',
        'discord_username',
        'discord_id',
        'password',
        'spec',
        'alts',
        'rank',
        'rank_goal',
        'raid_group',
        'note',
        'officer_note',
        'personal_note',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'email',
        'password',
        'remember_token',
        'discord_id',
        'personal_note',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->orderByDesc('position')->withTimestamps();
    }

    public function recipes() {
        $query = $this
            ->belongsToMany(Item::class, 'user_items', 'user_id', 'item_id')
            ->where('user_items.type', 'recipe')
            ->orderBy('order')
            ->withTimeStamps();

        return ($query);
    }

    public function received() {
        $query = $this
            ->belongsToMany(Item::class, 'user_items', 'user_id', 'item_id')
            ->where('user_items.type', 'received')
            ->orderBy('order')
            ->withTimeStamps();

        return ($query);
    }

    public function wishlist() {
        $query = $this
            ->belongsToMany(Item::class, 'user_items', 'user_id', 'item_id')
            ->where('user_items.type', 'wishlist')
            ->orderBy('order')
            ->withTimeStamps();

        return ($query);
    }

    // Fetch the user's roles from Discord and sync them
    public function fetchAndSyncRoles() {
        $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);
        $discordMember = $discord->guild->getGuildMember(['guild.id' => (int)env('GUILD_ID'), 'user.id' => (int)$this->discord_id]);
        $roles = Role::whereIn('discord_id', $discordMember->roles)->get()->keyBy('id')->keys()->toArray();
        $this->syncRoles($roles);
    }
}
