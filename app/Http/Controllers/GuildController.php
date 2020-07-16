<?php

namespace App\Http\Controllers;

use App\{Guild, Member, Role, User};
use Auth;
use Illuminate\Http\Request;
use RestCord\DiscordClient;
use Kodeine\Acl\Models\Eloquent\Permission;

class GuildController extends Controller
{
    const ADMIN_PERMISSIONS = 0x8;
    const MANAGEMENT_PERMISSIONS = 0x20;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser']);
    }

    /**
     * Show the guild registration.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegister()
    {
        return view('guild.register', []);
    }

    /**
     * Register a guild
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        $validationRules =  [
            'name'       => 'string|max:255|unique:guilds,name',
            'discord_id' => 'string|max:255|unique:guilds,discord_id',
            'bot_added'  => 'numeric|gte:1',
        ];

        $this->validate(request(), $validationRules);

        $input = request()->all();
        $user = Auth::user();

        // Verify that the bot is on the server
        $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);

        $roles = $discord->guild->getGuildRoles(['guild.id' => (int)$input['discord_id']]);

        $discordMember = $discord->guild->getGuildMember(['guild.id' => (int)$input['discord_id'], 'user.id' => (int)$user->discord_id]);

        $hasPermissions = false;

        // Go through each of the user's roles, and check to see if any of them have admin or management permissions
        // We're only going to let the user register this server if they have one of those permissions
        foreach ($discordMember->roles as $role) {
            $permissions = $roles[array_search($role, array_column($roles, 'id'))]->permissions;
            if (($permissions & self::ADMIN_PERMISSIONS) == self::ADMIN_PERMISSIONS || ($permissions & self::MANAGEMENT_PERMISSIONS) == self::MANAGEMENT_PERMISSIONS) {
                $hasPermissions = true;
                break;
            }
        }

        if (!$hasPermissions) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
               'permissions' => ["We couldn't find admin or management permissions on your account for that server. Have someone with one of those permissions register your guild."],
            ]);
            throw $error;
        }

        // Create the guild
        $guild = Guild::firstOrCreate(['name' => $input['name']],
            [
                'slug'       => slug($input['name']),
                'user_id'    => $user->id,
                'discord_id' => $input['discord_id'],
            ]);

        // Insert the roles associated with this Discord
        foreach ($roles as $role) {
            $role = Role::firstOrCreate(['discord_id' => $role->id],
                [
                    'name'        => $role->name,
                    'guild_id'    => $guild->id,
                    'slug'        => slug($role->name),
                    'description' => null,
                    'color'       => $role->color ? $role->color : null,
                    'position'    => $role->position,
                    'permissions' => $role->permissions,
                ]);
        }

        // Create a member for the current user
        $member = Member::firstOrCreate(['user_id' => $user->id, 'guild_id' => $guild->id], ['username' => $user->username]);

        // Attach the member's current roles from the guild discord
        $roles = Role::whereIn('discord_id', $discordMember->roles)->get()->keyBy('id')->keys()->toArray();
        $user->roles()->attach($roles);

        // Redirect to guild settings page; prompting the user to finish setup
        request()->session()->flash('status', 'Successfully registered guild.');
        return redirect()->route('guild.settings', ['guildSlug' => $guild->slug]);
    }

    /**
     * Show the guild settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with(['raids', 'roles'])->firstOrFail();

        // TODO: Validate can view this page for this guild

        return view('guild.settings', ['guild' => $guild]);
    }

    /**
     * Submit the guild settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function submitSettings($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with(['roles', 'raids'])->firstOrFail();

        // TODO: Validate user can update settings for this guild

        $validationRules =  [
            'name'           => 'string|max:255|unique:guilds,name,' . $guild->id,
            'calendar_link'  => 'nullable|string|max:255',
            'member_roles.*' => 'nullable|integer|exists:roles,discord_id',
        ];

        $this->validate(request(), $validationRules);

        $updateValues['name']            = request()->input('name');
        $updateValues['slug']            = slug(request()->input('name'));
        $updateValues['calendar_link']   = request()->input('calendar_link');
        $updateValues['member_role_ids'] = implode(array_filter(request()->input('member_roles')), ",");

        $guild->update($updateValues);

        request()->session()->flash('status', 'Guild settings updated.');
        return redirect()->route('guild.news', ['guildSlug' => $guild->slug]);
    }
}
