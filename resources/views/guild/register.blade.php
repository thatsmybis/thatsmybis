@extends('layouts.app')
@section('title', 'Register Guild - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8 col-xl-6 offset-lg-2 offset-xl-3">
            <h1>
                <span class="fas fa-fw fa-users-crown text-gold"></span>
                Register a Guild
            </h1>
        </div>
        <div class="col-12 col-lg-8 col-xl-6 offset-lg-2 offset-xl-3 mt-3 mb-3 pt-3 bg-lightest rounded">
            <p class="lead">This website uses your guild's Discord server to manage your members' access and permissions.</p>
            <p class="lead">Instructions:</p>
            <ol class="lead">
                <li>
                    Add
                    <a href="https://discord.com/api/oauth2/authorize?client_id={{ env('DISCORD_KEY') }}&permissions=0&redirect_uri={{ env('DISCORD_REDIRECT_URI') }}&scope=bot"
                        target="_blank" class="font-weight-bold">this bot</a>
                    to your Discord server.
                    <span class="text-muted">(requires server admin or management permissions)</span>
                </li>
                <li>
                    Fill out the form below.
                </li>
            </ol>
            <p class="lead">Once registered, invite your guild members by sharing the URL to your guild.</p>
        </div>
        <div class="col-12 col-lg-8 col-xl-6 offset-lg-2 offset-xl-3 mb-3 pt-3 bg-lightest rounded">
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

                <div class="form-group">
                    <label for="name" class="font-weight-bold">
                        <span class="text-muted fas fa-fw fa-users"></span>
                        Guild Name
                    </label>
                    <input required name="name" maxlength="36" type="text" class="form-control" placeholder="must be unique" value="{{ old('name') ? old('name') : null }}" />
                </div>

                <div class="form-group">
                    <label for="discord_id" class="font-weight-bold">
                        <span class="text-muted fab fa-fw fa-discord"></span>
                        Discord Server
                        <span class="text-muted font-weight-normal">ones you have admin permissions on</span>
                    </label>
                    <select name="discord_id_select" class="form-control">
                        <option value="">
                            —
                        </option>

                        @foreach ($guilds as $guild)
                            <option value="{{ $guild['id'] }}"
                                {{ $guild['registered'] ? 'disabled' : '' }}
                                {{ old('discord_id_select') ? (old('discord_id_select') == $guild['id'] ? 'selected' : '') : '' }}>
                                {{ $guild['registered'] ? '(already registered)' : '' }}
                                {{ $guild['name'] }}
                            </option>
                        @endforeach
                        @php
                            // Destroy variable so it doesn't mess with other templates
                            unset($guild);
                        @endphp
                    </select>
                    <span class="text-muted cursor-pointer" id="discord_id_toggle"
                        style="{{ old('discord_id') ? 'display:none;' : '' }}"
                        onclick="$('#discord_id').show();$('#discord_id_toggle').hide();">
                        <strong>OR</strong> click here to manually enter a server ID
                    </span>
                    <div class="" id="discord_id" style="{{ old('discord_id') ? '' : 'display:none;' }}">
                        <label for="discord_id" class="font-weight-light">
                            (optional)
                            <span class="sr-only">
                                paste your server's ID
                            </span>
                        </label>
                        <span class="text-muted">
                            <a href="https://support.discord.com/hc/en-us/articles/206346498-Where-can-I-find-my-User-Server-Message-ID-" target="_blank">
                                instructions
                            </a>
                            for finding a server ID
                        </span>
                        <input name="discord_id" maxlength="255" type="text" class="form-control" placeholder="paste your guild's server ID" value="{{ old('discord_id') ? old('discord_id') : null }}" />
                    </div>
                </div>

                <!-- Expansion -->
                <div class="form-group">
                    <label for="expansion_id" class="font-weight-bold">
                        <span class="text-muted fab fa-fw fa-battle-net"></span>
                        Expansion
                    </label>
                    <select name="expansion_id" class="form-control">
                        @foreach ($expansions as $expansion)
                            <option value="{{ $expansion->id }}"
                                {{ old('expansion_id') && old('expansion_id') == $expansion->id ? 'selected' : '' }}
                                {{ !$expansion->is_enabled ? 'disabled' : '' }}>
                                {{ $expansion->name_long }}
                                {{ !$expansion->is_enabled ? '(not yet supported)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group pt-3 pl-4">
                    <input class="form-check-input" type="checkbox" value="1" id="bot_added" onclick="toggleSubmit()">
                    <label class="form-check-label" for="bot_added">
                        I've added
                        <a href="https://discord.com/api/oauth2/authorize?client_id={{ env('DISCORD_KEY') }}&permissions=0&redirect_uri={{ env('DISCORD_REDIRECT_URI') }}&scope=bot"
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
