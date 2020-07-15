<?php

namespace App\Http\Controllers;

use App\{Content, Guild, Raid, Role, User};
use Auth;
use Illuminate\Http\Request;
use RestCord\DiscordClient;

class DashboardController extends Controller
{
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
     * Show the News page.
     *
     * @return \Illuminate\Http\Response
     */
    public function news($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with(['raids', 'roles'])->firstOrFail();
        $user = User::where('id', Auth::id())->with('roles')->first();

        $category = request()->input('category');

        if (!$category) {
            $category = 'my-feed';

            $userDiscordRoles = $user->roles->where(['guild_id' => $guild->id])->keyBy('discord_id')->keys();

            $userRaids = Raid::whereIn('discord_role_id', $userDiscordRoles)->get()->keyBy('id')->keys()->toArray();

            $content = Content::where(['category' => 'news', 'guild_id' => $guild->id])->orWhereIn('raid_id', $userRaids)->whereNull('removed_at')->with('user')->orderByDesc('created_at')->get();
        } else {
            $content = Content::where('category', $category)->whereNull('removed_at')->with('user')->orderByDesc('created_at')->get();
        }

        return view('news', [
            'category' => $category,
            'contents' => $content,
            'guild'    => $guild,
            'raids'    => $guild->raids,
        ]);
    }

    /**
     * Show the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->firstOrFail();
        return view('calendar', ['guild' => $guild]);
    }

    /**
     * Show the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarIframe($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->firstOrFail();
        $iframe = file_get_contents($guild->calendar_link); // 'https://calendar.google.com/calendar/embed?' .
        $iframe = str_replace('</head>','<link rel="stylesheet" href="http://' . $_SERVER['SERVER_NAME'] . '/css/googleCalendar.css" /></head>', $iframe);
        $iframe = str_replace('</title>','</title><base href="https://calendar.google.com/" />', $iframe);
        return $iframe;
    }

    /**
     * Show the roster page.
     *
     * @return \Illuminate\Http\Response
     */
    public function roster()
    {
        $userFields = [
            'id',
            'username',
            'discord_username',
            'spec',
            'alts',
            'rank',
            'rank_goal',
            'note',
        ];
        if (Auth::user()->hasRole(env('PERMISSION_RAID_LEADER'))) {
            $userFields[] = 'officer_note';
        }

        $members = User::select($userFields)
            ->whereNull('banned_at')
            ->with(['wishlist', 'recipes', 'received', 'roles'])
            ->orderBy('username')->get();

        $roles = Role::all();

        return view('roster', [
            'members' => $members,
            'roles'   => $roles,
        ]);
    }
}
