@extends('layouts.app')
@section('title', "FAQ - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row bg-light pt-5 pb-5 mb-5 rounded">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <h1>Frequently Asked Questions</h1>

            <ol>
                <li><a href="#how-to-loot-council">How do I loot council?</a></li>
                <li><a href="#what-does-this-do">What does this website do?</a></li>
                <li><a href="#getting-started">How do I get started?</a></li>
                <li><a href="#role-whitelisting">Why do my Discord users need a specific role to join?</a></li>
                <li><a href="#discord-requirement">Do I <em>need</em> to use Discord?</a></li>
                <!--<li><a href="#google-calendar">Google Calendar?</a></li>-->
                <li><a href="#name-origin">Where'd you get your name?</a></li>
                <li><a href="#feature-request">Can you add feature XYZ?</a></li>
                <li><a href="#how-to-help">I'd like to help out</a></li>
            </ol>

            <hr class="light">

            <h2 id="getting-started">How do I loot council?</h2>
            <p>
                We're glad you asked. This is the most comprehensive guide we could find: <a href="https://www.reddit.com/r/classicwow/comments/hnjsge/how_to_loot_council/" target="_none">How To Loot Council</a>
            </p>

            <hr class="light">

            <h2 id="what-does-this-do">What does this website do?</h2>
            <p>
                This website aims to offer a solution for keeping track of who has looted what in your guild and raids. It also aims to allow your raid members to let your guild's leadership know which items they are going for. Our solution to these problems is primarily aimed at guilds which use a loot council system, as opposed to something such as Dragon Kill Points (DKP).
            </p>
            <p>
                It is our hope that we can offer a better solution when compared to using spreadsheets to manage this logistical problem. If you have any suggestions for how we can do this better or would like to help out, please reach out to us <a href="{{ env('APP_DISCORD') }}" target="_blank">on our Discord</a>.
            </p>

            <hr class="light">

            <h2 id="getting-started">How do I get started?</h2>
            <p>
                Start by <a href="{{ route('guild.showRegister') }}">registering your guild's Discord server</a>, which requires you have Administrator permissions on your Discord server. Fill out the basic guild settings in the admin section once registered.
            </p>
            <p>
                Once you've done this preparation, you can then share the link to your guild's page with your guild members who are on your guild Discord server. Provided they have one of the allowed roles (you should have filled one or more of these out in your guild settings), they will be allowed to register on this website and then join your guild.
            </p>

            <hr class="light">

            <h2 id="role-whitelisting">Why do my Discord users need a specific role to join?</h2>
            <p>
                We require you to whitelist which members of your Discord server are allowed to register by giving them whitelisted roles that you choose. This is to prevent literally anyone who joins your Discord from crashing your guild on this website, which could be destructive.
            </p>
            <p>
                To set this up, you will need to create roles on your Discord server and assign them to your members. We recommend adding a 'Member' or 'Raider' role at the very least. Beyond that, we also suggest adding roles for 'Guild Master', 'Officer', and 'Raid Leader'. It is not required that your guild have this structure; it is simply how <em>most</em> successful guilds are structured.
            </p>
            <p>
                Once that's done, you might need to sync your Discord roles from the Roles page. Then, select the roles you want to whitelist in Guild Settings page. (only the guild's owner or guild master can see these pages)
            </p>

            <hr class="light">

            <h2 id="discord-requirement">Do I <em>need</em> to use Discord?</h2>
            <p>
                Yes. We designed this website to work with our guild and other major guilds on our server. We all use Discord, so we designed this website around our needs.
            </p>
            <p>
                We suggest you have a Discord server for your guild. We also suggest you create roles on your Discord server which you then assign to your guild members (roles such as 'Guild Master', 'Officer', 'Class Leader', 'Raider', 'Member'). These roles can then be imported onto this website and be used to define who has access to what (ie. a Raid Leader can assign loot to a raider, but a standard Member cannot).
            </p>

            <hr class="light">

            <!--
            <h2 id="google-calendar">Google Calendar?</h2>
            <p>
                You can create a Google Calendar and share a link to its public URL in the guild settings page. {{ env('APP_NAME') }} will then give your guild members access to a page that shows this calendar.
            </p>
            <p>
                <strong>If the calendar isn't working</strong>, follow <a href="https://i.imgur.com/IrhWvj7.png" target="_blank">these instructions</a>. <small>(in Google Calendar 1&gt; Calendar settings 2&gt; Settings for my calendars 3&gt; Choose your calendar 4&gt; Integrate calendar 5&gt; Public URL to this calendar)</small> If it's still not working, reach out to us <a href="{{ env('APP_DISCORD') }}" target="_blank">on our Discord</a>.
            </p>
            <p>
                This was chosen as the easiest possible solution for solving the problem of scheduling raids and events in a guild. Many guilds already use Google Sheets to manage a lot of information, and so it wasn't much of a stretch for those guilds to use Google Calendar amongst their officers as well.
            </p>
            <p>
                Why didn't we implement our own calendar? Calendars, timezones, and time tracking are immensely complicated concepts in programming. Implementing our own calendar may take several more times more work than the rest of this website combined. For more information, check out <a href="https://www.youtube.com/watch?v=-5wpm-gesOY" target="_blank">this video</a>.
            </p>
            <p>
                Don't like Google Calendar? If you have a good suggestion for another calendar for us to support, let us know <a href="{{ env('APP_DISCORD') }}" target="_blank">on our Discord</a> and we'll have a look.
            </p>

            <hr class="light">
            -->

            <h2 id="name-origin">Where'd you get your name?</h2>
            <p>
                It's an ongoing meme (and reality) that many players and classes in World of Warcraft Classic will attempt to claim pretty much every item as their Best in Slot ("That's my BIS"). We thought it sounded like an appropriate name for this website. Here's a <a href="https://www.youtube.com/watch?v=3lCC3r-Bxzk" target="_blank">tasteful little song</a> about this meme.
            </p>

            <hr class="light">

            <h2 id="feature-request">Can you add feature XYZ?</h2>
            <p>
                Maybe. Let us know <a href="{{ env('APP_DISCORD') }}" target="_blank">on our Discord</a> what you'd like to see, and we'll see if we can add it.
            </p>

            <hr class="light">

            <h2 id="how-to-help">I'd like to help out</h2>
            <p>
                Reach out to us <a href="{{ env('APP_DISCORD') }}" target="_blank">on our Discord</a> and we'll see if we can work together.
            </p>
        </div>
    </div>
</div>
@endsection
