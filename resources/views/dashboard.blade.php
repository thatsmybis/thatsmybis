@extends('layouts.app')
@section('title', config('app.name'))

@section('css')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 text-center mb-5">
            <h2 class="mt-5">
                Welcome, <strong>{{ Auth::user()->discord_username }}</strong>
            </h2>

            @if ($user->members->count() > 0)
                <ul>
                    @foreach ($user->members as $member)
                        <li>
                            <a href="">{{ $member->guild->name }}</a>
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

            <div class="mt-5 mb-5">
                <p class="font-weight-normal pt-3 text-4">
                    If you like, you can also
                </p>
                <a class="btn btn-light" href="{{ route('discordLogin') }}" title="Sign in with Discord" rel="nofollow">
                    <img class="discord-link" src="{{ asset('images/discord-logo.svg') }}" alt="" /> Register a Guild
                </a>
                <p class="text-muted pt-3">
                    (requires management access of your guild's discord)
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
