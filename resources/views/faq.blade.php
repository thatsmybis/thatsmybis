@extends('layouts.app')
@section('title', __("FAQ") . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row bg-light pt-5 pb-5 mb-5 rounded">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <h1>{{ __("Frequently Asked Questions") }}</h1>

            <h2>{{ __("Common Problems") }}</h2>
            <ol>
                <li><a href="#wrong-account">I can't see my guild</a></li>
                <li><a href="#not-seeing-guild">Why can't I see my guild?</a></li>
                <li><a href="#really">Really, I can't see my guild</a></li>
                <li><a href="#delete-stuff">How do I delete a (character/member/raid/guild/etc)?</a></li>
                <li><a href="#bot-offline">The bot is offline</a></li>
                <li><a href="#bot-missing">The bot is not in my server</a></li>
                <li><a href="#streamer-mode">Streamer Mode? Where are the officer notes?</a></li>
                <li><a href="#role-sync">Something is wrong with roles</a></li>
            </ol>

            <h2>Other FAQ</h2>
            <ol>
                <li><a href="#loot-tables">Where are the public loot tables?</a></li>
                <li><a href="#how-to-loot-council">How do I loot council?</a></li>
                <li><a href="#what-does-this-do">What does this website do?</a></li>
                <li><a href="#getting-started">How do I get started?</a></li>
                <li><a href="#discord-requirement">Do I <em>need</em> to use Discord?</a></li>
                <!--<li><a href="#google-calendar">Google Calendar?</a></li>-->
                <li><a href="#privacy">What information do you collect?</a></li>
                <li><a href="#name-origin">Where'd you get your name?</a></li>
                <li><a href="#feature-request">Can you add feature XYZ?</a></li>
                <li><a href="#how-to-help">I'd like to help out</a></li>
            </ol>

            <hr class="light">

            <div class="mt-5 mb-5">
                <h2 id="wrong-account">I can't see my guild(s)</h2>
                <div>
                    <p>
                        If you're the guild owner, you'll need to <a href="{{ route('guild.showRegister') }}" title="Register a Guild" rel="nofollow">register</a> the guild first. Otherwise:
                    </p>
                    <span class="font-weight-bold">Problem: Can't see the guild on the website!</span>
                    <ul>
                        <li>The guild owner on {{ config('app.name') }} may need to set roles under the Permissions section in the Guild Settings page.</li>
                        <li>You need to be in your guild's Discord and have a role that guild owner sets before you can join.</li>
                    </ul>
                </div>
                <div>
                    <span class="font-weight-bold">Problem: STILL can't see the guild on the website!</span>
                    <ul>
                        <li>Most likely: Your Discord account in the browser is signed into the wrong account. Yes, even if you only have one account. Yes, even if the name looks similar.</li>
                        <li>The username and last four digits on the site MUST match your main Discord account.</li>
                        <li class="no-bullet">
                            <img src="http://s3.amazonaws.com/thatsmybis.com/wrong_discord_account.png" alt="an image showing what to check to confirm that you are in the wrong Discord account">
                        </li>
                        <li class="font-weight-bold">TRIPLE CHECK THAT THE ACCOUNT NAMES ARE EXACTLY THE SAME, INCLUDING THE NUMBERS AT THE END</li>
                        <li>Go to <a href="https://discord.com/app" target="_blank">https://discord.com/app</a> and click the gear icon at the bottom.</li>
                        <li>Then, click the Log Out link at the bottom.</li>
                        <li>Log back in on Discord's website, and <span class="font-weight-bold">make sure it's your primary account.</span></li>
                        <li>Log out of That's My BIS, and back in.</li>
                        <li>If that didn't work, try a different browser or Incognito/Private Browsing.</li>
                    </ul>
                </div>
            </div>

            <hr class="light">

            <div class="mt-5 mb-5">
                <h2 id="not-seeing-guild">Why can't I see my guild?</h2>
                <p>
                    If you're already logged in, double check that the username and <strong>last four characters</strong> at the top of <a href="{{ route('home') }}">the homepage</a> match your username and <strong>last four characters</strong> in your Discord client. It's pretty common for people to accidentally be logged into a different Discord account in their browser and not even know it!
                </p>
                <p>
                    If the username and/or last four characters on the homepage <strong>don't match</strong> your account in your Discord client, visit Discord's website and log out. Log back in to Discord, and make sure it gives you the right account. Log out of {{ env('APP_NAME') }} and back in, and you should be all set!
                </p>
                <p>
                    Still not working? Ask for help <a href="{{ env('APP_DISCORD') }}" target="_blank">on my Discord</a>.
                </p>
            </div>

            <hr class="light">

            <div class="mt-5 mb-5">
                <h2 id="really">Really, I can't see my guild</h2>
                <p class="text-warning">
                    Very common problem. The sections above should solve it. Your Discord account in the browser and the app are probably different, which is why you can't see your guild.
                </p>

                <img src="http://s3.amazonaws.com/thatsmybis.com/wrong_discord_account.png" alt="an image showing what to check to confirm that you are in the wrong Discord account">
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="delete-stuff">How do I delete a (character/member/raid/guild/etc)?</h2>
                <p>
                    In order to keep accurate records, none of these can be permanently deleted; only disabled, made inactive, cancelled, etc.
                    <br>
                    As time goes on, I'll see what I can do for improving this. All of my development time is focused on adding new features right now.
                </p>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="bot-offline">The bot is offline</h2>
                <div>
                    <ul>
                        <li>This is okay. It's supposed to be offline. It's just there to see who is in your server and their roles.</li>
                        <li>The bot has no other functionality at the moment.</li>
                    </ul>
                </div>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="bot-missing">The bot is not in my server</h2>
                <div>
                    @include('guild/partials/addTheBot')
                </div>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="streamer-mode">Streamer Mode? Where are the officer notes?</h2>
                <div>
                    <p>Click the icon in the top left to toggle this mode.</p>
                    <img src="http://s3.amazonaws.com/thatsmybis.com/streamer_mode.png" alt="an image showing where to toggle Streamer Mode">
                </div>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="role-sync">Something is wrong with the roles</h2>
                <p>
                    The roles you see beside members are cached from when they were last online. They will only update when they come back online.
                    <br>
                    Don't worry, if you changed their roles in Discord, their access permissions will update when they next visit the website.
                </p>
            </div>

            <hr class="light">

            <div class="mt-5">
                <h2 id="loot-tables">Where are the public loot tables?</h2>
                <p>
                    <a href="{{ route('loot') }}" target="_blank">Here</a>
                </p>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="how-to-loot-council">How do I loot council?</h2>
                <p>
                    I'm glad you asked. This is the most comprehensive guide I could find: <a href="https://www.reddit.com/r/classicwow/comments/hnjsge/how_to_loot_council/" target="_none">How To Loot Council</a>
                </p>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="what-does-this-do">What does this website do?</h2>
                <p>
                    I aim to give loot councils an easy way to fairly distribute loot by providing them with the necessary information for them to make their decisions. At a glance, you can see which members are going for which items, what loot they've received in the past, any notes they may have, and all packaged in a clean and easy to read interface.
                </p>
                <p>
                    For raiders, you can clearly and easily communicate to loot council and fellow raiders what loot you're going for, making it much harder to be accidentally overlooked. You can even use my system to identify loot that the rest of your raid may have overlooked, giving you an edge in snagging some extra gear!
                </p>
                <p>
                    If you have any questions or would like to help out, please reach out to me <a href="{{ env('APP_DISCORD') }}" target="_blank">on my Discord</a>.
                </p>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="getting-started">How do I get started?</h2>
                <p>
                    Start by <a href="{{ route('guild.showRegister') }}">registering your guild's Discord server</a>, which requires you have Administrator permissions on your Discord server. Fill out the basic guild settings in the admin section once registered.
                </p>
                <p>
                    Once you've done this preparation, you can then share the link to your guild's page with your guild members who are on your guild Discord server. Provided they have one of the allowed roles (if specified any in your guild settings), they will be able to join your guild's page.
                </p>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="discord-requirement">Do I <em>need</em> to use Discord?</h2>
                <p>
                    Yes. I designed this website to work with my old guild and other major guilds on my server. My guild and I used Discord, so I designed this website around my guild's needs.
                </p>
                <p>
                    I suggest you have a Discord server for your guild. I also suggest you create roles on your Discord server which you then assign to your guild members (roles such as 'Guild Master', 'Officer', 'Class Leader', 'Raider', 'Member'). These roles can then be imported onto this website and be used to define who has access to what (ie. a Raid Leader can assign loot to a raider, but a standard Member cannot).
                </p>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="privacy">What information do you collect?</h2>
                <p>
                    TL;DR Your Discord username, ID, avatar ID, and roles for any servers you register/join. Since this project's source code is public, you can review the data I collect <a href="https://github.com/thatsmybis/thatsmybis/blob/master/app/Http/Controllers/Auth/LoginController.php#L91" target="_blank">right here</a> and <a href="https://github.com/thatsmybis/thatsmybis/blob/master/app/Http/Controllers/Auth/LoginController.php#L45" target="_blank">here</a>.
                </p>
                <p>
                    Your username and ID are so I can verify it's you when you're logging in. Roles are so I can give you the proper permissions in your guild if you're a raider, raid leader, officer, or guild master.
                </p>
                <p>
                    Of the available Discord <a href="https://discord.com/developers/docs/topics/oauth2#shared-resources-oauth2-scopes" target="_blank">OAuth2 Scopes</a> (info you allow Discord to share with me when you register or sign in), I'm only using 'identify' and 'guilds'. Guilds scope data isn't stored anywhere; it's only used to (a) identify which guilds are registered on the system that you belong to, and (b) provide a dropdown of available servers when a guild master is registering a guild.
                </p>
                <p>
                    I'm also using Google Analytics to keep track of how the site is doing. Firefox has built-in features to block Google Analytics, which should be enabled by default. (however, I somehow doubt Google Chrome does the same...)
                </p>
            </div>
            <hr class="light">
            <!--
            <h2 id="google-calendar">Google Calendar?</h2>
            <p>
                You can create a Google Calendar and share a link to its public URL in the guild settings page. {{ env('APP_NAME') }} will then give your guild members access to a page that shows this calendar.
            </p>
            <p>
                <strong>If the calendar isn't working</strong>, follow <a href="https://i.imgur.com/IrhWvj7.png" target="_blank">these instructions</a>. <small>(in Google Calendar 1&gt; Calendar settings 2&gt; Settings for my calendars 3&gt; Choose your calendar 4&gt; Integrate calendar 5&gt; Public URL to this calendar)</small> If it's still not working, reach out to me <a href="{{ env('APP_DISCORD') }}" target="_blank">on my Discord</a>.
            </p>
            <p>
                This was chosen as the easiest possible solution for solving the problem of scheduling raids and events in a guild. Many guilds already use Google Sheets to manage a lot of information, and so it wasn't much of a stretch for those guilds to use Google Calendar amongst their officers as well.
            </p>
            <p>
                Why didn't I implement my own calendar? Calendars, timezones, and time tracking are immensely complicated concepts in programming. Implementing my own calendar may take several more times more work than the rest of this website combined. For more information, check out <a href="https://www.youtube.com/watch?v=-5wpm-gesOY" target="_blank">this video</a>.
            </p>
            <p>
                Don't like Google Calendar? If you have a good suggestion for another calendar for me to support, let me know <a href="{{ env('APP_DISCORD') }}" target="_blank">on my Discord</a> and I'll have a look.
            </p>

            <hr class="light">
            -->
            <div class="mt-5 mb-5">
                <h2 id="name-origin">Where'd you get your name?</h2>
                <p>
                    It's an ongoing meme (and reality) that many players and classes in World of Warcraft Classic will attempt to claim pretty much every item as their Best in Slot ("That's my BIS"). I thought it sounded like an appropriate name for this website, as I've literally designed it to help raiders tell their loot council "Hey, that's my BIS, give it here."
                </p>
                <p>
                    Someone even made a <a href="https://www.youtube.com/watch?v=3lCC3r-Bxzk" target="_blank">tasteful little song</a> about this meme.
                </p>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="feature-request">Can you add feature XYZ?</h2>
                <p>
                    Maybe. Let me know <a href="{{ env('APP_DISCORD') }}" target="_blank">on my Discord</a> what you'd like to see, and I'll see if I can add it. Almost all <a href="https://github.com/thatsmybis/thatsmybis/projects/1" target="_blank">features added</a> post-launch have come from user requests.
                </p>
            </div>
            <hr class="light">
            <div class="mt-5 mb-5">
                <h2 id="how-to-help">I'd like to help out</h2>
                <p>
                    Reach out to me <a href="{{ env('APP_DISCORD') }}" target="_blank">on my Discord</a> and I'll see if we can work together. Or, jump straight into the <a href="https://github.com/thatsmybis/thatsmybis" target="_blank">source code</a>.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
