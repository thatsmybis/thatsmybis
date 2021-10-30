<?php

namespace App;

use App\{Guild, Member};
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        // 'email',
        // 'email_verified_at',
        'locale',
        'discord_id',
        'discord_username',
        'discord_avatar',
        'discord_token',
        'discord_refresh_token',
        'discord_token_expiry',
        'is_streamer_mode',
        'banned_at',
        'ads_disabled_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'email',
        'is_admin',
        'discord_id',
        'password',
        'remember_token',
        'discord_token',
        'discord_refresh_token',
        'discord_token_expiry',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function guilds()
    {
        return $this->hasMany(Guild::class, 'user_id');
    }

    public function members() {
        return $this->hasMany(Member::class, 'user_id');
    }
}
