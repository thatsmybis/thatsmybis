<?php

namespace App\Http\Controllers;

use App\{Content, Role, User};
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
     * Show the Dashboard page.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $user = Auth::user();

        // $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);

        // dd(
        //     $discord->guild->getGuild(['guild.id' => (int)env('GUILD_ID')]),
        //     $discord->guild->getGuildMember(['guild.id' => (int)env('GUILD_ID'), 'user.id' => (int)$user->discord_id]),
        // );


        $content = Content::whereNull('removed_at')->with('user')->orderByDesc('created_at')->get();
        return view('dashboard', ['contents' => $content]);
    }

    /**
     * Show the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar()
    {
        return view('calendar');
    }

    /**
     * Show the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarIframe()
    {
        $iframe = file_get_contents('https://calendar.google.com/calendar/embed?src=kb05a7c6hee4eb1b2dge8niro0%40group.calendar.google.com&ctz=America%2FNew_York');
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
        $roles = Role::all();
        $members = User::whereNull('banned_at')->with('roles')->orderBy('username')->get();
        return view('roster', [
            'members' => $members,
            'roles'   => $roles,
        ]);
    }
}
