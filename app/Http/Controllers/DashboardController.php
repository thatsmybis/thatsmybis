<?php

namespace App\Http\Controllers;

use App\{Content, User};
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'sessionHasDiscordToken', 'seeUser']);
    }

    /**
     * Show the Dashboard page.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $news = Content::where('is_news', 1)->whereNull('removed_at')->with('user')->orderByDesc('created_at')->get();
        return view('dashboard', ['news' => $news]);
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
        $content = file_get_contents('https://calendar.google.com/calendar/embed?src=kb05a7c6hee4eb1b2dge8niro0%40group.calendar.google.com&ctz=America%2FNew_York');
        $content = str_replace('</head>','<link rel="stylesheet" href="http://' . $_SERVER['SERVER_NAME'] . '/css/googleCalendar.css" /></head>', $content);
        $content = str_replace('</title>','</title><base href="https://calendar.google.com/" />', $content);
        return $content;
    }

    /**
     * Show the roster page.
     *
     * @return \Illuminate\Http\Response
     */
    public function roster()
    {
        $members = User::orderBy('username')->get();
        return view('roster', ['members' => $members]);
    }
}
