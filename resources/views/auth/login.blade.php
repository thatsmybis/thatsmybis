@extends('layouts.app')
@section('title', 'Login - ' . config('app.name'))

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
        <div class="text-center col-12 p-5">
            <a class="btn btn-light" href="{{ route('discordLogin') }}" title="Sign in with Discord" rel="nofollow">
                <img class="discord-link" src="{{ asset('images/discord-logo.svg') }}" alt="" /> Sign In
            </a>
        </div>
    </div>
</div>
@endsection
