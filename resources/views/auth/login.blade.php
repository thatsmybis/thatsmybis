@extends('layouts.app')
@section('title', 'Register - ' . config('app.name'))

@section('headerScripts')
<!-- reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
function registerSubmit(token) {
    document.getElementById("register-form").submit();
}
</script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="text-center col-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 m-5">
            @if (Auth::guest())
                <div class="">
                    <a class="btn btn-light" href="{{ route('discordLogin') }}" title="Sign in with Discord" rel="nofollow">
                        <img class="discord-link" src="{{ asset('images/discord-logo.svg') }}" alt="" /> Sign In
                    </a>
                </div>
            @elseif (Auth::user())
                <div class="col-md-12">
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                        <button type="submit" class="btn btn-primary">
                            Log Out
                        </button>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
