@extends('layouts.app')
@section('title', config('app.name'))

@section('css')
@endsection

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12 text-center mt-5 mb-5">
            <h2 class="">
                Welcome, <span class="text-discord font-weight-bold">{{ Auth::user()->discord_username }}</span>
            </h2>
        </div>

        <div class="col-md-8 col-sm-10 col-12 offset-md-2 offset-sm-1 text-center mt-5 mb-5">
            @if ($user->members->count() > 0)
                <h3 class="font-weight-normal mb-3">
                    <span class="fas fa-fw fa-users text-muted"></span>
                    Your Guilds
                </h3>
                <ul class="no-bullet no-indent">
                    @foreach ($user->members as $member)
                        <li class="bg-lightest mt-3 mb-3 p-3">
                            <h2>
                                <a href="{{ route('member.show', ['guildSlug' => $member->guild->slug, 'username' => $member->username]) }}" class="text-uncommon font-weight-medium">
                                    &lt;{{ $member->guild->name }}&gt;
                                </a>
                            </h2>
                            <ul class="list-inline">
                                @foreach ($member->characters as $character)
                                    <li class="list-inline-item bg-tag rounded pt-0 pl-2 pb-1 pr-2 m-2">
                                        <a href="{{route('character.show', ['guildSlug' => $member->guild->slug, 'name' => $character->name]) }}"
                                            class="text-{{ $character->class ? strtolower($character->class) : '' }}">
                                            {{ $character->name }}
                                        </a>
                                    </li>
                                @endforeach
                                <li class="list-inline-item bg-tag rounded pt-0 pl-2 pb-1 pr-2 m-2">
                                    <a href="{{ route('character.showCreate', ['guildSlug' => $member->guild->slug]) }}">
                                        <span class="fas fa-plus"></span>
                                        create character
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="mt-5 mb-5">
                    <p class="font-weight-normal pt-3 text-4">
                        You don't belong to any guilds yet
                    </p>
                    <p class="font-weight-normal pt-3 text-4">
                        Ask your guild leader for a link to your guild's page
                    </p>
                </div>
            @endif
        </div>

        @if ($existingGuilds)
            <div class="col-md-8 col-sm-10 col-12 offset-md-2 offset-sm-1 text-center mt-5 mb-5">
                <ul class="no-bullet no-indent">
                    @foreach ($existingGuilds as $existingGuild)
                        <li class="bg-lightest mt-3 mb-3 p-3">
                            <h2>
                                <span class="text-uncommon font-weight-medium">
                                    &lt;{{ $existingGuild->name }}&gt;
                                </span>
                            </h2>
                            <ul class="list-inline">
                                <li class="list-inline-item bg-tag rounded pt-0 pl-2 pb-1 pr-2 m-2">
                                    <a href="{{ route('guild.home', ['guildSlug' => $existingGuild->slug]) }}"
                                        class="btn btn-success">
                                        <span class="fas fa-plus"></span>
                                        Join Guild
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="col-12 text-center mb-5">
            <div class="mt-5 mb-5">
                <p class="font-weight-normal pt-3 text-4">
                    Are you a Guild Master?
                </p>
                <a class="btn btn-light" href="{{ route('guild.showRegister') }}" title="Register a Guild" rel="nofollow">
                    <img class="discord-link" src="{{ asset('images/discord-logo.svg') }}" alt="" /> Register a Guild
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
