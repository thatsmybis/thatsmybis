@extends('layouts.app')
@section('title', $user->username . "'s Profile - " . config('app.name'))

@section('content')

<div class="container-fluid p-5">
    <div class="col-xs-12">
        <h1>
            <span class="text-{{ strtolower($user->class) }}">
                {{ $user->username }}
            </span>
            @if ($canEdit)
                <small><a href="{{ route('showUser', ['id' => $user->id, 'username' => $user->username]) }}?edit=1">edit</a></small>
            @endif
            <br>
            <small>
                {{ $user->spec }} {{ $user->class }}
            </small>
        </h1>

        <div class="row mb-3">
            <div class="col-12">
                <span class="text-muted font-weight-bold">Professions</span>
            </div>
            <div class="col-12">
                {{ $user->professions }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <span class="text-muted font-weight-bold">Recipes</span>
            </div>
            <div class="col-12">
                {{ $user->recipes }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <span class="text-muted font-weight-bold">Alts</span>
            </div>
            <div class="col-12">
                {{ $user->alts }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <span class="text-muted font-weight-bold">Rank</span>
            </div>
            <div class="col-12">
                {{ $user->rank }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <span class="text-muted font-weight-bold">Rank Goal</span>
            </div>
            <div class="col-12">
                {{ $user->rank_goal }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <span class="text-muted font-weight-bold">Wishlist</span>
            </div>
            <div class="col-12">
                {{ $user->wishlist }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <span class="text-muted font-weight-bold">Loot Received</span>
            </div>
            <div class="col-12">
                {{ $user->loot_received }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <span class="text-muted font-weight-bold">Notes</span>
            </div>
            <div class="col-12">
                {{ $user->note }}
            </div>
        </div>

        @if ($showOfficerNote)
            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Officer Notes</span>
                </div>
                <div class="col-12">
                    {{ $user->officer_note }}
                </div>
            </div>
        @endif
    </div>
</div>


@endsection
