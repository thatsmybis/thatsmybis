<?php

namespace App\Http\Controllers;

use App\{User};
use Auth;
use Illuminate\Http\Request;
use LaravelRestcord\Discord\Discord;

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
        // LaravelRestcord\Discord\Discord::class
        $discord = new Discord();
        dd($discord);
        return view('dashboard');
    }
}
