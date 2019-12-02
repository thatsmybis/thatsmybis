<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="{{ route('home') }}"><span class="text-druid">&lt;Aftershock&gt;</span></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            @if (Auth::user())
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('home') }}">News</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('roster') }}">Roster</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('calendar') }}">Calendar</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('contentIndex') }}">Resources</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('showUser', ['id' => Auth::user()->id, 'username' => Auth::user()->username]) }}">My Profile</a>
                </li>
                @if (Auth::user()->hasRole('admin|guild_master|officer|raider'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="adminNavDropdown">
                            <a class="dropdown-item" href="{{ route('guild.raids') }}">Raids</a>
                            <a class="dropdown-item" href="{{ route('guild.roles') }}">Roles</a>
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
