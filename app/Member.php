<?php

namespace App;

use App\{Character, Content, Guild, Role, User, Raid};
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Facades\Log;
use Kodeine\Acl\Traits\HasRole;

class Member extends Model
{
    use HasRole {
        // Rename a function from this so that we can override it and still call it.
        // (see hasPermission())
        hasPermission as protected traitHasPermission;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'slug',
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

    public function hasPermission($permission, $operator = null) {
        // Call the function from our trait, but override it if the current user is a
        // badass SUPER ADMIN!
        return ($this->traitHasPermission($permission, $operator = null) || isSuper());
    }

    /**
     * Users can have many roles.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')->orderByDesc('position')->withTimestamps();
    }

    public function raidsWithViewPermissions() {

        $raids = $this->guild->raids;
        $restrictWishPrioList = false;
        
        /*
        // get all raids
        // where restrict_wish_prio_list is null (disabled) or I have the actual role
        $someRaids = $this->guild->raids
            ->leftJoin('characters', function ($join) {
                $join->on('characters.raid_id', 'raids.id');
            })
            ->leftJoin('members', function ($join) {
                $join->on('members.id', 'characters.member_id');
            })
            ->leftJoin('role_user', function ($join) {
                $join->on('role_user.user_id', 'members.id');
            })
            ->whereNull('restrict_wish_prio_list_role')
            ->orWhere('raids.restrict_wish_prio_list_role', '=', 'role_user.role_id');                      

        Log::info($someRaids);
        */

        // check if we have to restrict the wish/prio list
        foreach($raids as $raid) {
            if( $raid->restrict_wish_prio_list_role) {
                $restrictWishPrioList=true;
            }
        }

        if ($restrictWishPrioList) {
            $raidsWithListViewPermissions = array();
            foreach ($this->roles as $myRole) {
                foreach($raids as $raid) {  
                    if ($myRole->id === $raid->restrict_wish_prio_list_role || !$raid->restrict_wish_prio_list_role) { // if only one raid has filtering active for whatever reason
                        array_push($raidsWithListViewPermissions, $raid->id);
                    }
                }
            }
            return $raidsWithListViewPermissions;
        }
        
        
 

        return $raids->keyBy('id')->keys();
    }

    /**
     * Users can have many permissions overridden from permissions.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_id')->withTimestamps();
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
        $member = self::firstOrCreate(['user_id' => $user->id, 'guild_id' => $guild->id], ['username' => $user->username, 'slug' => slug($user->username)]);
        $member->load('roles');

        if ($discordMember) {
            // Attach the member's current roles from the guild discord
            $member->syncRoles($guild, $discordMember);
        }

        return $member;
    }

    /**
     * Sync a member's roles with the roles they have in Discord. If the member has Discord roles that
     * aren't already loaded into the guild, they will be added to the guild.
     *
     * Note that $discordMember->roles is an array of Discord role ID's attached to the member, straight
     * from Discord.
     *
     * @param App\Guild                        $guild         The member's guild. Passing this allows us
     *                                                        to eager load and avoid extra db queries.
     * @param RestCord\Model\Guild\GuildMember $discordMember The Discord Member, along with their roles
     */
    public function syncRoles($guild, $discordMember) {
        if ($discordMember->roles) {
            $memberRoles = $this->roles->keyBy('discord_id')->keys()->toArray();

            $rolesToDetach = array_diff($memberRoles, $discordMember->roles);
            $rolesToAttach = array_diff($discordMember->roles, $memberRoles);

            if ($rolesToDetach) {
                // Looks like you don't actually have those roles anymore. RIP!
                $this->roles()->detach($this->roles->whereIn('discord_id', $rolesToDetach));
            }

            if ($rolesToAttach) {
                // Looks like you have some new roles! Let's fetch them from the database...
                $roles = Role::whereIn('discord_id', $rolesToAttach)->get();

                $attachRolesArray = [];

                foreach ($rolesToAttach as $roleToAttach) {
                    $role = $roles->where('discord_id', $roleToAttach)->first();
                    if ($role) {
                        // We have that role on file, let's prep it for attachment
                        $attachRolesArray[] = $role->id;
                    } else {
                        // Seems we don't have that role on file. Let's update our roles and add it in the process.
                        $guild = Role::syncWithDiscord($guild)['guild'];

                        // Now try again...
                        $role = $guild->roles->where('discord_id', $roleToAttach)->first();
                        // This shouldn't ever assert false; we literally just added the role (classic comment right here)
                        if ($role) {
                            $attachRolesArray[] = $role->id;
                        } else {
                            // Eh... I guess we'll just let it go.
                            Log::error('Tried to add Discord role "' . $roleToAttach . '" to guild "' . $guild->id . '/' . $guild->name . '", but failed. Not sure why. Literally just loaded Discord\'s roles into the guild. Probably worth investigating. https://imgs.xkcd.com/comics/unreachable_state.png');
                        }
                    }
                }

                // Attach all of the roles.
                $this->roles()->attach($attachRolesArray);
            }
        } else {
            // Detach all roles
            $this->roles()->detach();
        }

        return true;
    }
}
