<?php

namespace App;

use App\{Member};
use Illuminate\Database\Eloquent\Model;
use Kodeine\Acl\Traits\HasPermission;
use RestCord\DiscordClient;
/**
 * I copied over the Role class from the kodeine\laravel-acl library because... reasons?
 * I did this a while back. Not sure why. But here it is!
 * Maybe it was just an easier way to inherit/modify it?
 */
class Role extends Model
{
    use HasPermission;

    /**
     * The attributes that are fillable via mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'discord_id',
        'guild_id',
        'slug',
        'description',
        'color',
        'position',
        'discord_permissions',
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Use the slug of the Role
     * instead of the ID.
     *
     * @return string
     */
    public function getRouteKeyName() {
        return 'slug';
    }

    /**
     * Roles can belong to many users.
     *
     * @return Model
     */
    public function users()
    {
        // Why are we using role_user and not role_member? The library we're using assumes our model is called 'user' is why.
        return $this->belongsToMany(Member::class, 'role_users', 'role_id', 'user_id')->withTimestamps();
    }

    /**
     * List all permissions
     *
     * @return mixed
     */
    public function getPermissions()
    {
        return \Cache::remember(
            'acl.getPermissionsInheritedById_'.$this->id,
            now()->addMinutes(config('acl.cacheMinutes', 1)),
            function () {
                return $this->getPermissionsInherited();
            }
        );
    }

    /**
     * Checks if the role has the given permission.
     *
     * @param string $permission
     * @param string $operator
     * @param array  $mergePermissions
     * @return bool
     */
    public function hasPermission($permission, $operator = null, $mergePermissions = [])
    {
        $operator = is_null($operator) ? $this->parseOperator($permission) : $operator;

        $permission = $this->hasDelimiterToArray($permission);
        $permissions = $this->getPermissions() + $mergePermissions;

        // make permissions to dot notation.
        // create.user, delete.admin etc.
        $permissions = $this->toDotPermissions($permissions);

        // validate permissions array
        if ( is_array($permission) ) {

            if ( ! in_array($operator, ['and', 'or']) ) {
                $e = 'Invalid operator, available operators are "and", "or".';
                throw new \InvalidArgumentException($e);
            }

            $call = 'canWith' . ucwords($operator);

            return $this->$call($permission, $permissions);
        }

        // validate single permission
        return isset($permissions[$permission]) && $permissions[$permission] == true;
    }

    /**
     * @param $permission
     * @param $permissions
     * @return bool
     */
    protected function canWithAnd($permission, $permissions)
    {
        foreach ($permission as $check) {
            if ( ! in_array($check, $permissions) || ! isset($permissions[$check]) || $permissions[$check] != true ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $permission
     * @param $permissions
     * @return bool
     */
    protected function canWithOr($permission, $permissions)
    {
        foreach ($permission as $check) {
            if ( in_array($check, $permissions) && isset($permissions[$check]) && $permissions[$check] == true ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the color inherited from Discord
     *
     * @return string A hex value
     */
    public function getColor() {
        $color = null;

        if ($this->color) {
            $color = dechex($this->color);

            // If it's too short, keep adding prefixed zero's till it's long enough
            while (strlen($color) < 6) {
                $color = '0' . $color;
            }
        } else {
            $color = 'FFF';
        }
        return '#' . $color;
    }

    /**
     * Sync the database's roles with those found on a guild's Discord server.
     *
     * @param App\Guild $guild A guild, ideally eager loaded with its existing roles() association.
     *
     * @return array Counts of the number of roles added, removed, and updated. Also the updated guild object.
     */
    public static function syncWithDiscord($guild) {
        $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);

        // List of roles that we already have (local)
        $localRoles = $guild->roles;

        // List of roles fetched from Discord (remote)
        $remoteRoles = $discord->guild->getGuildRoles(['guild.id' => $guild->discord_id]);

        $updatedCount = 0;
        $addedCount   = 0;
        $removedCount = 0;

        // Iterate over the roles in remote
        foreach ($remoteRoles as $remoteRole) {
            if (!$localRoles->contains('discord_id', $remoteRole->id)) {
            // Role not found in local: Add role to local
                Role::create([
                    'name'                => $remoteRole->name,
                    'guild_id'            => $guild->id,
                    'discord_id'          => (int)$remoteRole->id,
                    'discord_permissions' => $remoteRole->permissions,
                    'position'            => $remoteRole->position,
                    'color'               => $remoteRole->color,
                    'slug'                => slug($remoteRole->name),
                    'description'         => '',
                ]);
                $addedCount++;
            } else {
            // Role found in local: Update role in local to match remote
                $localRole = $localRoles->where('discord_id', $remoteRole->id)->first();

                $localRole->color = $remoteRole->color ? $remoteRole->color : null;
                $localRole->name = $remoteRole->name;
                $localRole->slug = slug($remoteRole->name);
                $localRole->position = $remoteRole->position;

                $localRole->save();
                $updatedCount++;
            }
        }

        // Iterate over the roles in local
        foreach ($localRoles as $localRole) {
            $found = false;
            foreach ($remoteRoles as $remoteRole) {
                if ($remoteRole->id == $localRole->discord_id) {
                    $found = true;
                    break;
                }
            }

            // Role in local doesn't exist in remote: delete local role
            if (!$found) {
                $localRole->delete();
                $removedCount++;
            }
        }

        // Eager load the guild object with the latest roles before passing it back
        $guild->load('roles');

        return [
            'addedCount'   => $addedCount,
            'updatedCount' => $updatedCount,
            'removedCount' => $removedCount,
            'guild'        => $guild,
        ];
    }
}
