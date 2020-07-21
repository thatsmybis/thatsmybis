@extends('layouts.app')
@section('title', $character->name . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-3">
                <div class="col-12 pt-2 bg-lightest rounded">
                    @include('character/partials/header', ['headerSize' => 1, 'showEdit' => true, 'showIcon' => false])
                </div>
            </div>

            <div class="row mb-3 pt-3 bg-light rounded">
                <div class="col-12 mb-4">
                    <a href="{{ route('character.loot', ['guildSlug' => $guild->slug, 'name' => $character->name]) }}" class="text-4">
                        <span class="fas fa-fw fa-pencil"></span>
                        edit loot
                    </a>
                </div>

                <div class="col-12 mb-2">
                    <span class="text-legendary font-weight-bold">
                        <span class="fas fa-fw fa-scroll-old"></span>
                        Wishlist
                    </span>
                </div>
                <div class="col-12 pb-3">
                    @if ($character->wishlist->count() > 0)
                        <ol class="">
                            @foreach ($character->wishlist as $item)
                                <li class="">
                                    @include('partials/item', ['wowheadLink' => false])
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="pl-4">
                            —
                        </div>
                    @endif
                </div>

                <div class="col-12 mb-2">
                    <span class="text-success font-weight-bold">
                        <span class="fas fa-fw fa-sack"></span>
                        Loot Received
                    </span>
                </div>
                <div class="col-12 pb-3">
                    @if ($character->received->count() > 0)
                        <ol class="">
                            @foreach ($character->received as $item)
                                <li class="">
                                    @include('partials/item', ['wowheadLink' => false])
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="pl-4">
                            —
                        </div>
                    @endif
                </div>

                <div class="col-12 mb-2">
                    <span class="text-gold font-weight-bold">
                        <span class="fas fa-fw fa-book"></span>
                        Recipes
                    </span>
                </div>
                <div class="col-12 pb-3">
                    @if ($character->recipes->count() > 0)
                        <ol class="">
                            @foreach ($character->recipes as $item)
                                <li class="">
                                    @include('partials/item', ['wowheadLink' => false])
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="pl-4">
                            —
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mb-3 pt-3 pb-3 bg-light rounded">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-comment-alt-lines"></span>
                        Public Note
                    </span>
                </div>
                <div class="col-12 mb-3 pl-4">
                    {{ $character->public_note ? $character->public_note : '—' }}
                </div>

                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-shield"></span>
                        Officer Note
                    </span>
                </div>
                <div class="col-12 mb-3 pl-4">
                    {{ $character->officer_note ? $character->officer_note : '—' }}
                </div>

                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-lock"></span>
                        Personal Note
                    </span>
                </div>
                <div class="col-12 pl-4">
                    {{ $character->personal_note ? $character->personal_note : '—' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
