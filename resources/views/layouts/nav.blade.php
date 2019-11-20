<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">Aftershock</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('home') }}">Home</a>
            </li>
        </ul>
        <div class="my-2 my-lg-0">
            @if (Auth::guest())
                <a class="btn btn-light" href="{{ route('discordLogin') }}" title="Sign in with Discord" rel="nofollow">
                    <img class="discord-link" src="{{ asset('images/discord-logo.svg') }}" alt="" /> Sign In
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
