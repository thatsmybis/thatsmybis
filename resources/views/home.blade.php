@extends('layouts.app')
@section('title', config('app.name'))

@section('css')
<link rel="stylesheet" type="text/css" href="/css/stars.css">
@endsection

@section('css')

@endsection

@section('bodyClass', 'bg-gradient')

@section('content')
<div id="stars"></div>
<div id="stars2"></div>
<div id="stars3"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 text-center mb-5">
            <h1 class="mt-5 mb-5 text-1">
                <span class="font-weight-bold">{{ env('APP_NAME') }}</span>
            </h1>
        <p class="mt-1 font-weight-normal mb-5 text-3">
                A tool for loot council guilds
            </p>

            <p class="font-weight-normal pt-3 text-4">
                easily keep track of your raid's
                <br>
                loot distribution
            </p>

            <div class="mt-5 mb-5">
                <a class="btn btn-light" href="{{ route('discordLogin') }}" title="Sign in with Discord" rel="nofollow">
                    <img class="discord-link" src="{{ asset('images/discord-logo.svg') }}" alt="" /> Sign In
                </a>
            </div>

            <div class="mt-5 mb-5 pt-5">
                <blockquote class="reddit-card"><a href="https://www.reddit.com/r/classicwow/comments/hnjsge/how_to_loot_council/">How To Loot Council</a> from <a href="http://www.reddit.com/r/classicwow">r/classicwow</a></blockquote>
                <script async src="//embed.redditmedia.com/widgets/platform.js" charset="UTF-8"></script>
            </div>
        </div>
    </div>
</div>
@endsection
