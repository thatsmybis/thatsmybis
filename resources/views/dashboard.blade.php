@extends('layouts.app')
@section('title', config('app.name'))

@section('css')
@endsection

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h2 class="mt-5">
                Welcome, <strong>{{ Auth::user()->discord_username }}</strong>
            </h2>
        </div>

        <div class="col-md-8 col-sm-10 col-12 offset-md-2 offset-sm-1 text-center mb-5">
            @if ($user->members->count() > 0)
                <h4 class="mb-3">Your guilds</h4>
                <ul class="list-group">
                    @foreach ($user->members as $member)
                        <li class="list-group-item text-4">
                            <a href="{{ route('guild.news', ['guildSlug' => $member->guild->slug]) }}" class="text-success font-weight-bold">
                                &lt;{{ $member->guild->name }}&gt;
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="mt-5 mb-5">
                    <p class="font-weight-normal pt-3 text-4">
                        You don't belong to any guilds yet.
                    </p>
                    <p class="font-weight-normal pt-3 text-4">
                        Ask your guild leader for a link to your guild's page.
                    </p>
                </div>
            @endif
        </div>

        <div class="col-12 text-center mb-5">
            <div class="mt-5 mb-5">
                <p class="font-weight-normal pt-3 text-4">
                    If you like, you can also
                </p>
                <a class="btn btn-light" href="{{ route('guild.showRegister') }}" title="Register a Guild" rel="nofollow">
                    <img class="discord-link" src="{{ asset('images/discord-logo.svg') }}" alt="" /> Register a Guild
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
