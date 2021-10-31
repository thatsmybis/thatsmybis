<?php

namespace App\Http\Controllers;

use App\{Expansion, Guild, User};
use Auth;
use Illuminate\Http\Request;
use RestCord\DiscordClient;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'about', 'caliPrivacy', 'contact', 'donate', 'faq', 'privacy', 'terms');
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
     * Show the Privacy Policy page for California users.
     *
     * @return \Illuminate\Http\Response
     */
    public function caliPrivacy()
    {
        return view('caliPrivacy');
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
                'members' => function ($query) {
                    return $query
                        ->select('members.*') // Otherwise fetches guild fields as well, resulting in some bugs...
                        ->leftJoin('guilds', 'guilds.id', 'members.guild_id')
                        ->orderBy('guilds.name');
                },
                'members.characters',
                'members.characters.raidGroup',
                'members.guild',
            ]);

            $existingGuilds = null;

            // Fetch guilds the user can join that already exist on this website
            if ($user->discord_token) {

                // Cache to results
                $discordGuilds = Cache::remember('user:' . $user->id . ':discordGuilds', env('DISCORD_ROLE_CACHE_SECONDS'),
                    function () use ($user) {
                        $discord = new DiscordClient([
                            'token'     => $user->discord_token,
                            'tokenType' => 'OAuth',
                        ]);
                        // TODO: Handle this failing; sometimes gives 401 or 500 or whatever.
                        return $discord->user->getCurrentUserGuilds();
                    }
                );

                if ($discordGuilds) {
                    $guildIds = [];
                    foreach ($discordGuilds as $discordGuild) {
                        $discordGuildIds[$discordGuild->id] = $discordGuild->id;
                    }

                    $currentGuildIds = $user->members->pluck('guild_id')->toArray();

                    $existingGuilds = Guild::whereIn('discord_id', $discordGuildIds)
                        ->whereNotIn('id', $currentGuildIds)
                        ->orderBy('guilds.name')
                        ->get();
                }
            }

            return view('dashboard', [
                'existingGuilds' => $existingGuilds,
                'expansions'     => Expansion::all(),
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

    /**
     * Show the page with instructions on how to provide translations.
     *
     * @return \Illuminate\Http\Response
     */
    public function translations()
    {
        return view('translations');
    }
}
