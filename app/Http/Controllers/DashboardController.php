<?php

namespace App\Http\Controllers;

use App\{Character, Content, Guild, Member, Raid, Role, User};
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
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                return $query->where('members.user_id', Auth::id());
            },
            'raids',
            'roles',
        ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        $user = User::where('id', Auth::id())->with('roles')->first();

        // TODO: Permissions

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
            'category'      => $category,
            'contents'      => $content,
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raids'         => $guild->raids,
        ]);
    }

    /**
     * Show the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                return $query->where('members.user_id', Auth::id());
            },
        ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        // TODO: Permissions
        return view('calendar', ['currentMember' => $currentMember, 'guild' => $guild]);
    }

    /**
     * Show the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarIframe($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->firstOrFail();

        // TODO: Permissions

        $iframe = file_get_contents($guild->calendar_link); // 'https://calendar.google.com/calendar/embed?' .
        $iframe = str_replace('</head>','<link rel="stylesheet" href="http://' . $_SERVER['SERVER_NAME'] . '/css/googleCalendar.css" /></head>', $iframe);
        $iframe = str_replace('</title>','</title><base href="https://calendar.google.com/" />', $iframe);
        return $iframe;
    }

    /**
     * Show the default guild page
     *
     * @return \Illuminate\Http\Response
     */
    public function home($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                return $query->where('members.user_id', Auth::id());
            },
        ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        return redirect()->route('member.show', [
            'guildSlug' => $guild->slug,
            'username'  => $currentMember->username
        ]);
    }

    /**
     * Show the roster page.
     *
     * @return \Illuminate\Http\Response
     */
    public function roster($guildSlug)
    {
        $guild = Guild::where('slug', $guildSlug)->with([
            'members' => function ($query) {
                return $query->where('members.user_id', Auth::id());
            },
            'raids'
        ])->firstOrFail();

        $currentMember = $guild->members->where('user_id', Auth::id())->first();

        if (!$currentMember) {
            abort(403, 'Not a member of that guild.');
        }

        // TODO: Validate user can view this roster

        $characterFields = [
            'characters.id',
            'characters.member_id',
            'characters.guild_id',
            'characters.name',
            'characters.level',
            'characters.race',
            'characters.class',
            'characters.spec',
            'characters.profession_1',
            'characters.profession_2',
            'characters.rank',
            'characters.rank_goal',
            'characters.raid_id',
            'characters.public_note',
            'characters.inactive_at',
        ];

        // TODO permissions for showing officer note
        if (true) {
            $characterFields[] = 'characters.officer_note';
        }

        $characters = Character::select($characterFields)
            ->where('characters.guild_id', $guild->id)
            ->whereNull('characters.inactive_at')
            ->with([/*'member', 'member.user.roles',*/'raid', 'recipes', 'received', 'wishlist'])
            ->orderBy('characters.name')
            ->get();

        return view('roster', [
            'characters'    => $characters,
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raids'         => $guild->raids,
        ]);
    }
}
