<?php

namespace App\Http\Controllers;

use App\{Content, RaidGroup};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
    public function news($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load(['raidGroups', 'roles']);

        // if we reimplement: We want member not user
        // if we reimplement this: Change this if we re-implement the news section
        $user = request()->get('guild')->load('roles');

        $category = request()->input('category');

        if (!$category) {
            $category = 'my-feed';

            $userDiscordRoles = $user->roles->where(['guild_id' => $guild->id])->keyBy('discord_id')->keys();

            $userRaidGroups = RaidGroup::whereIn('discord_role_id', $userDiscordRoles)->get()->keyBy('id')->keys()->toArray();

            $content = Content::where(['category' => 'news', 'guild_id' => $guild->id])->orWhereIn('raid_group_id', $userRaidGroups)->whereNull('removed_at')->with('user')->orderByDesc('created_at')->get();
        } else {
            $content = Content::where('category', $category)->whereNull('removed_at')->with('user')->orderByDesc('created_at')->get();
        }

        return view('news', [
            'category'      => $category,
            'contents'      => $content,
            'currentMember' => $currentMember,
            'guild'         => $guild,
            'raidGroups'    => $guild->raidGroups,
        ]);
    }

    /**
     * Show the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        return view('calendar', ['currentMember' => $currentMember, 'guild' => $guild]);
    }

    /**
     * Show the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarIframe($guildId, $guildSlug)
    {
        $guild = request()->get('guild');

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
    public function home($guildId, $guildSlug)
    {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        $guild->load('expansion');

        return redirect()->route('member.show', [
            'guildId'      => $guild->id,
            'guildSlug'    => $guild->slug,
            'memberId'     => $currentMember->id,
            'usernameSlug' => $currentMember->slug
        ]);
    }
}
