<?php

namespace App;

use App\{
    BaseModel,
    Character,
    Content,
    Expansion,
    Item,
    Member,
    Raid,
    RaidGroup,
    Role,
    User,
};

use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class Guild extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'user_id',
        'discord_id',
        'expansion_id',
        'faction',
        'warcraftlogs_token',
        'warcraftlogs_refresh_token',
        'warcraftlogs_token_expiry',
        'warcraftlogs_member_id',
        'warcraftlogs_guild_id',
        'admin_role_id',
        'gm_role_id',
        'officer_role_id',
        'raid_leader_role_id',
        'auditor_role_id',
        'class_leader_role_id',
        'member_role_ids',
        'message',
        'calendar_link',
        'is_attendance_hidden',
        'attendance_decay_days',
        'is_prio_private',
        'is_prio_disabled',
        'is_received_locked',
        'is_wishlist_private',
        'is_wishlist_locked',
        'wishlist_locked_exceptions', // A list of exceptions, delimited by commas.
        'wishlist_names', // A list of names, delimited by the bar character "|".
        'is_prio_autopurged',
        'is_wishlist_autopurged',
        'is_wishlist_disabled',
        'max_wishlist_items',
        'current_wishlist_number',
        'prio_show_count',
        'do_sort_items_by_instance',
        'is_raid_group_locked',
        'tier_mode',
        'disabled_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'discord_id',
        'warcraftlogs_token',
        'warcraftlogs_refresh_token',
        'warcraftlogs_token_expiry',
        'admin_role_id',
        'gm_role_id',
        'officer_role_id',
        'raid_leader_role_id',
        'class_leader_role_id',
        'member_role_ids',
        'calendar_link',
    ];

    const TIER_MODE_NUM = 'num';
    const TIER_MODE_S   = 's';

    const TIERS = [
        1 => 'S',
        2 => 'A',
        3 => 'B',
        4 => 'C',
        5 => 'D',
        6 => 'F',
    ];

    const FACTION_BEST  = 'h';
    const FACTION_WORST = 'a';

    // Includes hidden and removed characters
    public function allCharacters() {
        return $this
            ->hasMany(Character::class)
            ->select([
                'characters.*'
            ])
            ->orderBy('characters.name');
    }

    public function allCharactersWithAttendance() {
        return Character::addAttendanceQuery($this->allCharacters(), $this->id);
    }

    // Includes banned and inactive members
    public function allMembers() {
        return $this->hasMany(Member::class)->orderBy('username');
    }

    public function allRaidGroups() {
        return $this->hasMany(RaidGroup::class)->orderBy('name');
    }

    // Excludes hidden and removed characters
    public function characters() {
        return $this
            ->hasMany(Character::class)
            ->select([
                'characters.*'
            ])
            ->whereNull('characters.inactive_at')
            ->orderBy('characters.name');
    }

    // Gets characters and their attendance stats
    // Excludes hidden and removed characters
    public function charactersWithAttendance() {
        return Character::addAttendanceQuery($this->characters(), $this->id);
    }

    public function content() {
        return $this->hasMany(Content::class)->whereNull('removed_at')->orderByDesc('created_at');
    }

    public function expansion() {
        return $this->belongsTo(Expansion::class);
    }

    // Other guilds registered under the same Discord server ID
    public function guilds() {
        return $this->hasMany(Guild::class, 'discord_id', 'discord_id')->where('guilds.id', '!=', $this->id);
    }

    public function items() {
        return $this->belongsToMany(Item::class, 'guild_items', 'guild_id', 'item_id')
            ->withTimeStamps()
            ->withPivot(['created_by', 'updated_by', 'note', 'priority', 'tier'])
            ->orderBy('items.name');
    }

    // Excludes banned members and inactive
    public function members() {
        return $this->hasMany(Member::class)->whereNull('banned_at')->whereNull('inactive_at')->orderBy('username');
    }

    public function raids()
    {
        return $this->hasMany(Raid::class)->orderByDesc('date');
    }

    public function roles()
    {
        return $this->hasMany(Role::class)->orderBy('name');
    }

    public function raidGroups() {
        return $this->hasMany(RaidGroup::class)->whereNull('disabled_at')->orderBy('name');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    /* Various functions */

    static public function getFactions() {
        return [
            self::FACTION_BEST  => self::FACTION_BEST,
            self::FACTION_WORST => self::FACTION_WORST,
        ];
    }

    public function getMemberRoleIds() {
        if ($this->member_role_ids) {
            return explode(',', $this->member_role_ids);
        } else {
            return [];
        }

    }

    public function getWishlistLockedExceptions() {
        return explode(',', $this->wishlist_locked_exceptions);
    }

    public function getWishlistNames() {
        return $this->wishlist_names ? explode('|', $this->wishlist_names) : null;
    }

    // For fetching cached characters with attendance
    public static function getAllCharactersWithAttendanceCached($guild) {
        $cacheKey = 'guild:' . $guild->id . 'charactersWithAttendance';

        // Create a different cache key if the user is using a raid group filter for attendance
        $raidGroupIdFilter = request()->get('raidGroupIdFilter');
        if ($raidGroupIdFilter) {
            $cacheKey .= ':raidGroup:' . $raidGroupIdFilter;
        }

        if (request()->get('bustCache')) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, env('CACHE_GUILD_ATTENDANCE_CHARACTERS_SECONDS', 60), function () use ($guild) {
            return $guild->allCharactersWithAttendance()->get();
        });
    }

    /**
     * SHORT AND SIMPLE NAME IS SHORT AND SIMPLE.
     * Returns all of the characters and all the stuff associated with them.
     * Since it goes through the work of looking them up, also returns some of passed in member's permissions.
     *
     * @param bool   $showOfficerNote
     * @param bool   $showPrios
     * @param bool   $showWishlist
     * @param bool   $showInactive
     *
     * @return array
     */
    public function getCharactersWithItemsAndPermissions($showOfficerNote, $showPrios, $showWishlist, $viewPrioPermission, $showInactive, $allWishlists) {
        $characterFields = [
            'characters.id',
            'characters.member_id',
            'characters.guild_id',
            'characters.name',
            'characters.slug',
            'characters.level',
            'characters.race',
            'characters.class',
            'characters.archetype',
            'characters.spec',
            'characters.spec_label',
            'characters.profession_1',
            'characters.profession_2',
            'characters.rank',
            'characters.rank_goal',
            'characters.raid_group_id',
            'characters.is_alt',
            'characters.public_note',
            'characters.inactive_at',
            'members.username',
            'members.slug AS member_slug',
            'users.discord_username',
            'users.discord_id',
            'members.is_wishlist_unlocked',
            'members.is_received_unlocked',
            'raid_groups.name AS raid_group_name',
            'raid_group_roles.color AS raid_group_color',
        ];

        if ($showOfficerNote) {
            $characterFields[] = 'characters.officer_note';
        }

        $query = Character::select($characterFields)
            ->leftJoin('members', function ($join) {
                $join->on('members.id', 'characters.member_id');
            })
            ->leftJoin('users', function ($join) {
                $join->on('users.id', 'members.user_id');
            })
            ->leftJoin('raid_groups', function ($join) {
                $join->on('raid_groups.id', 'characters.raid_group_id');
            })
            ->leftJoin('roles AS raid_group_roles', function ($join) {
                $join->on('raid_group_roles.id', 'raid_groups.role_id');
            })
            ->where('characters.guild_id', $this->id)
            ->orderBy('characters.name')
            ->with([
                'received',
                'secondaryRaidGroups' => function ($query) {
                    return $query
                        ->select([
                            'raid_groups.id',
                            'raid_groups.name',
                            'roles.color',
                        ])
                        ->leftJoin('roles', function ($join) {
                            $join->on('roles.id', 'raid_groups.role_id');
                        });
                    },
                ]);

        $query = Character::addAttendanceQuery($query, $this->id);

        if (!$showInactive) {
            $query = $query->whereNull('characters.inactive_at');
        }

        if ($showPrios) {
            if ($this->prio_show_count && !$viewPrioPermission) {
                $query = $query->with(['prios' => function ($query) {
                    return $query->where([
                        ['character_items.order', '<=', $this->prio_show_count],
                    ]);
                }]);
            } else {
                $query = $query->with('prios');
            }
        }

        if ($showWishlist) {
            if ($allWishlists) {
                // NOTE that this will output the relation 'allWishlists' and NOT 'wishlist'
                $query = $query->with('allWishlists');
            } else {
                $query = $query->with('wishlist');
            }
        }

        $characters = $query->get();

        if (!$showOfficerNote) {
            // Hide officer notes on item assignments
            $characters->each(function ($character) {
                $character->received->each(function ($item) {
                    $item->pivot->makeHidden(['officer_note']);
                });
            });
        }

        // Ugh idk, I just want to not have to make all the calls again to check for these permissions... I'd rather just reuse them by sending them back.
        return [
            'characters'      => $characters,
            'showOfficerNote' => $showOfficerNote,
            'showPrios'       => $showPrios,
            'showWishlist'    => $showWishlist,
         ];
    }

    // Returns the maximum level for characters in this guild
    public function getMaxLevel() {
        if ($this->expansion_id === 1) {
            return 60;
        } else if ($this->expansion_id === 2) {
            return 70;
        } else if ($this->expansion_id === 3) {
            return 80;
        } else {
            return 60;
        }
    }

    /**
     * Returns non-archived characters, plus characters passed in.
     * Useful for existing resources that don't want to drop any archived characters already associated.
     */
    public function getSelectableCharacters($mandatoryCharacters) {
        $allCharacters = Character::mergeAttendance($this->allCharacters()->get(), Guild::getAllCharactersWithAttendanceCached($this));

        $whitelistCharacterIds = null;

        if ($mandatoryCharacters && $mandatoryCharacters instanceof Collection) {
            $whitelistCharacterIds = $mandatoryCharacters->keyBy('id')->keys()->toArray();
        }

        return $allCharacters->filter(function ($character, $key) use ($whitelistCharacterIds) {
            // Character ID exists in raid's list of characters OR character is NOT archived
            return !$character->inactive_at || ($whitelistCharacterIds && in_array($character->id, $whitelistCharacterIds));
        });
    }

    public static function tiers() {
        return self::TIERS;
    }
}
