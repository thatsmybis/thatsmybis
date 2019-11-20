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
