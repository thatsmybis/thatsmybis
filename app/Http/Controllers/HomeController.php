<?php

namespace App\Http\Controllers;

use App\{Guild, User};
use Auth;
use Illuminate\Http\Request;
use RestCord\DiscordClient;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'about', 'contact', 'faq', 'privacy', 'terms');
        $this->middleware('seeUser');
    }

    /**
     * Show the about page.
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Show the contact page.
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Show the donate page.
     *
     * @return \Illuminate\Http\Response
     */
    public function donate()
    {
        return view('donate');
    }

    /**
     * Show the faq page.
     *
     * @return \Illuminate\Http\Response
     */
    public function faq()
    {
        return view('faq');
    }

    /**
     * Show the home page for users who haven't signed in.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check() && Auth::user()) {
        // Authenticated users default to a different page
            $user = request()->get('currentUser');
            $user->load([
                'members',
                'members.characters',
                'members.characters.raid',
                'members.guild',
            ]);

            $existingGuilds = null;

            // Fetch guilds the user can join that already exist on this website
            if ($user->discord_token) {

                $discord = new DiscordClient([
                    'token' => $user->discord_token,
                    'tokenType' => 'OAuth',
                ]);

                $guilds = $discord->user->getCurrentUserGuilds();

                if ($guilds) {
                    $guildIds = [];
                    foreach ($guilds as $guild) {
                        $guildIds[$guild->id] = $guild->id;
                    }

                    // Remove guilds they're already a member of
                    foreach ($user->members as $member) {
                        unset($guildIds[$member->guild->discord_id]);
                    }

                    $existingGuilds = Guild::whereIn('discord_id', $guildIds)->get();
                }
            }

            return view('dashboard', [
                'existingGuilds' => $existingGuilds,
                'user'           => $user,
            ]);
        } else {
            return view('home');
        }
    }

    /**
     * Show the Privacy Policy page.
     *
     * @return \Illuminate\Http\Response
     */
    public function privacy()
    {
        return view('privacy');
    }

    /**
     * Show the Terms and Conditions page.
     *
     * @return \Illuminate\Http\Response
     */
    public function terms()
    {
        return view('terms');
    }
}
