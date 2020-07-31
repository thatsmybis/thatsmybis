@extends('layouts.app')
@section('title', 'Register Guild - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">
            <h1>
                <span class="fas fa-fw fa-users-crown text-gold"></span>
                Register a Guild
            </h1>
        </div>
        <div class="col-12 col-lg-6 offset-lg-3 mt-3 mb-3 pt-3 bg-lightest rounded">
            <p class="lead">This app uses your guild's Discord server to manage your members' access and permissions.</p>
            <p class="lead">Instructions:</p>
            <ol class="lead">
                <li>
                    Add
                    <a href="https://discord.com/api/oauth2/authorize?client_id=645311036785950721&permissions=0&redirect_uri=https%3A%2F%2Fthatsmybis.com%2Fauth%2Fdiscord%2Fcallback&scope=bot"
                        target="_blank" class="font-weight-bold">this bot</a>
                    to your Discord server. (requires server admin or management permissions)
                </li>
                <li>
                    Copy your server's ID by following
                    <a href="https://support.discord.com/hc/en-us/articles/206346498-Where-can-I-find-my-User-Server-Message-ID-" target="_blank">these instructions</a>.
                </li>
                <li>
                    Fill out the form below.
                </li>
            </ol>
            <p class="lead">Once registered, invite your guild members by sharing the URL to your guild.</p>
        </div>
        <div class="col-12 col-lg-6 offset-lg-3 mb-3 pt-3 bg-lightest rounded">
            @if (count($errors) > 0)
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <form class="form-horizontal" role="form" method="POST" action="{{ route('guild.register') }}">
                {{ csrf_field() }}

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-users"></span>
                                Guild Name
                            </label>
                            <input name="name" maxlength="36" type="text" class="form-control" placeholder="must be unique" value="{{ old('name') ? old('name') : null }}" />
                        </div>

                        <div class="form-group">
                            <label for="discord_id" class="font-weight-bold">
                                <span class="text-muted fab fa-fw fa-discord"></span>
                                Discord Server ID
                                &nbsp;
                            </label>
                            <input name="discord_id" maxlength="255" type="text" class="form-control" placeholder="paste your guild's server ID" value="{{ old('discord_id') ? old('discord_id') : null }}" />
                        </div>

                        <div class="form-group pt-3 pl-4">
                            <input class="form-check-input" type="checkbox" value="1" id="bot_added" onclick="toggleSubmit()">
                            <label class="form-check-label" for="bot_added">
                                I've added
                                <a href="https://discord.com/api/oauth2/authorize?client_id=645311036785950721&permissions=0&redirect_uri=https%3A%2F%2Fthatsmybis.com%2Fauth%2Fdiscord%2Fcallback&scope=bot"
                                    target="_blank">
                                    the bot
                                </a>
                                to my server
                            </label>
                        </div>

                        <div class="form-group pt-3">
                            <button disabled class="btn btn-success" id="submit_button">
                                <span class="fas fa-fw fa-check"></span>
                                A little submit button ofc
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSubmit() {
        var checkBox = document.getElementById("bot_added");
        var button = document.getElementById("submit_button");

        if (checkBox.checked == true) {
            button.disabled = false;
        } else {
            button.disabled = true;
        }
    }
</script>
@endsection
