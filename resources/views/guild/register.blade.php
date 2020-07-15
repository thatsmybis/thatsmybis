@extends('layouts.app')
@section('title', 'Register Guild - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">
            <h1>Register a Guild</h1>
            <p class="pt-3 text-4">
                First, add the
                <a href="https://discord.com/api/oauth2/authorize?client_id=645311036785950721&permissions=0&redirect_uri=https%3A%2F%2Fthatsmybis.com%2Fauth%2Fdiscord%2Fcallback&scope=bot"
                    target="_blank"
                    class="font-italic font-weight-bold">
                    <span class="text-legendary">{{ env('APP_NAME') }}</span> bot
                </a>
                to your guild's Discord server. <span class="small text-muted">(requires guild permissions)</span> This allows us to verify who's on the server and their permissions. You need to leave the bot on the server so long as you want to keep using <span class="text-legendary">{{ env('APP_NAME') }}</span>.
            </p>
            <p class="pt-3 text-4">
                Then, you're going to want to fill out this little form here:
            </p>
        </div>
        <div class="col-12 col-lg-6 offset-lg-3">
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
                                Guild Name
                            </label>
                            <input name="name" maxlength="255" type="text" class="form-control" placeholder="it's gotta be unique" value="{{ old('name') ? old('name') : null }}" />
                        </div>

                        <div class="form-group">
                            <label for="discord_id" class="font-weight-bold">
                                Discord Server ID
                                &nbsp;
                                <a href="https://support.discord.com/hc/en-us/articles/206346498-Where-can-I-find-my-User-Server-Message-ID-" target="_blank" class="small text-muted">
                                    where can I find this?
                                </a>
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
                            <button disabled class="btn btn-success" id="submit_button">A little submit button ofc</button>
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
