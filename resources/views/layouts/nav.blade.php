<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="{{ route('home') }}"><span class="text-legendary font-weight-bold">{{ isset($guild) && $guild->name ? $guild->name : env('APP_NAME') }}</span></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            @if (Auth::user() && isset($guild) && $guild)
                <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.news']) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guild.news', ['guildSlug' => $guild->slug]) }}">News</a>
                </li>
                @if ($guild->calendar_link)
                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.calendar']) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('guild.calendar', ['guildSlug' => $guild->slug]) }}">Calendar</a>
                    </li>
                @endif
                <li class="nav-item {{ in_array(Route::currentRouteName(), ['guild.roster']) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guild.roster', ['guildSlug' => $guild->slug]) }}">Roster</a>
                </li>
                <li class="nav-item {{ in_array(Route::currentRouteName(), ['contentIndex', 'showContent']) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('contentIndex', ['guildSlug' => $guild->slug]) }}">Resources</a>
                </li>
                <li class="nav-item {{ in_array(Route::currentRouteName(), ['dashboard']) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('showMember', ['guildSlug' => $guild->slug, 'id' => Auth::user()->id, 'username' => Auth::user()->username]) }}">My Profile</a>
                </li>
                @if (true) <!-- TODO permissions -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="adminNavDropdown">
                            <a class="dropdown-item" href="{{ route('guild.raids', ['guildSlug' => $guild->slug]) }}">Raids</a>
                            <a class="dropdown-item" href="{{ route('guild.roles', ['guildSlug' => $guild->slug]) }}">Roles</a>
                            <a class="dropdown-item" href="{{ route('guild.settings', ['guildSlug' => $guild->slug]) }}">Settings</a>
                            <!-- Can't get permissions working right now, so I'm disabling this
                                <a class="dropdown-item" href="">Permissions</a> -->
                        </div>
                    </li>
                @endif
            @endif
        </ul>
        <div class="my-2 my-lg-0">
            @if (Auth::guest())
                <a class="" href="{{ route('discordLogin') }}" title="Sign in with Discord" rel="nofollow">
                    Sign In
                </a>
            @else
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    <span class="text-grey fal fa-fw fa-sign-out"></span> Sign Out ({{ Auth::user()->username }})
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            @endif
        </div>
    </div>
</nav>
