@extends('layouts.app')
@section('title', config('app.name'))

@section('css')
<link rel="stylesheet" type="text/css" href="{{ loadScript('floatyDots.css', 'css') }}">
@endsection

{{-- overflow-x-hidden is for the floaty dots that can wander too far horizontally... --}}
@section('bodyClass', 'bg-gradient overflow-x-hidden')

@section('content')
<!-- floaty dot crap in the background -->
<div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div><div class="circle-container"><div class="circle"></div></div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 text-center mt-5 mb-5 pt-5">
            <h1 class="mt-5 pt-5 mb-1 text-1">
                <span class="font-weight-bold">{{ env('APP_NAME') }}</span>
            </h1>
            <h3 class="mb-5 text-expansion-1">
                {{ __("Season of Mastery</span>") }}
            </h3>
            <h2 class="mt-1 font-weight-normal mb-5 text-3">
                {{ __("A tool for World of Warcraft") }}
                <br>
                {{ __("loot management") }}
            </h2>

            <h3 class="font-weight-normal mb-5 pt-3 text-4">
                {{ __("easily keep track of your raid's") }}
                <br>
                {{ __("loot distribution") }}
            </h3>

            <div class="mt-5 mb-5">
                <a class="btn btn-light" href="{{ route('discordLogin') }}" title="Sign in with Discord" rel="nofollow">
                    <img class="discord-link" src="{{ asset('images/discord-logo.svg') }}" alt="" /> {{ __("Sign In") }}
                </a>
            </div>

            <h4 class="text-5 pt-3">
                {{ __("see what people are wishlisting in") }}
                <a href="{{ route('loot.wishlist', ['expansionName' => 'tbc']) }}" class="font-weight-bold text-{{ getExpansionColor(2) }}">{{ __("TBC") }}</a>
                {{ __("and") }}
                <a href="{{ route('loot.wishlist', ['expansionName' => 'classic']) }}" class="font-weight-bold text-{{ getExpansionColor(1) }}">{{ __("Classic") }}</a>
            </h4>

            <p class="mt-3">
                {{ __("or view our public") }} <a href="{{ route('loot') }}">{{ __("loot tables") }}</a>
            </p>

            <div class="pt-5 mt-5 mb-5">
                <p class="font-weight-bold text-4">
                    {{ __("Preview video") }}
                </p>
                <iframe style="width:100%;max-width:960px;height:540px;" src="https://www.youtube.com/embed/vOJuNdYs_2w" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>

            <div class="pt-5 mt-5 mb-5">
                <p class="font-weight-bold text-4">
                    {{ __("Longer preview video with most of the new features and stuff") }}
                </p>
                <iframe style="width:100%;max-width:960px;height:540px;" src="https://www.youtube.com/embed/hj_tqjxy6sY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>

            <p class="mt-3">
                {!! __("supports Classic, Classic Fresh, Classic Season of Mastery, The Burning Crusade (TBC),<br> and eventually Wrath of the Lich King (WotLK) upon release") !!}
            </p>
        </div>
    </div>
</div>
@endsection
