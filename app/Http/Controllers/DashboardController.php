<?php

namespace App\Http\Controllers;

use App\{User};
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
        $this->middleware('sessionHasDiscordToken');
    }

    /**
     * Show the Dashboard page.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view('dashboard');
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
        $members = User::all();
        return view('roster', ['members' => $members]);
    }
}
