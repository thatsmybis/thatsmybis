@extends('layouts.app')
@section('title', $character->name . " - " . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12">
                    @include('characters/partials/header', ['headerSize' => 1, 'showEdit' => true])
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <hr class="light">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 mb-4">
                    <a href="{{ route('character.loot', ['guildSlug' => $guild->slug, 'name' => $character->name]) }}" class="text-4">
                        <span class="fas fa-fw fa-pencil"></span>
                        edit loot
                    </a>
                </div>

                <div class="col-12">
                    <span class="text-legendary font-weight-bold">
                        <span class="fas fa-fw fa-scroll-old"></span>
                        Wishlist
                    </span>
                </div>
                <div class="col-12">
                    @if ($character->wishlist->count() > 0)
                        <ol class="">
                            @foreach ($character->wishlist as $item)
                                <li class="">
                                    @include('partials/item', ['wowheadLink' => true])
                                </li>
                            @endforeach
                        </ol>
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-success font-weight-bold">
                        <span class="fas fa-fw fa-sack"></span>
                        Loot Received
                    </span>
                </div>
                <div class="col-12">
                    @if ($character->received->count() > 0)
                        <ol class="">
                            @foreach ($character->received as $item)
                                <li class="">
                                    @include('partials/item', ['wowheadLink' => true])
                                </li>
                            @endforeach
                        </ol>
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-gold font-weight-bold">
                        <span class="fas fa-fw fa-book"></span>
                        Recipes
                    </span>
                </div>
                <div class="col-12">
                    @if ($character->recipes->count() > 0)
                        <ol class="">
                            @foreach ($character->recipes as $item)
                                <li class="">
                                    @include('partials/item', ['wowheadLink' => true])
                                </li>
                            @endforeach
                        </ol>
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <hr class="light">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-comment-alt-lines"></span>
                        Public Note
                    </span>
                </div>
                <div class="col-12">
                    {{ $character->public_note ? $character->public_note : '—' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-shield"></span>
                        Officer Note
                    </span>
                </div>
                <div class="col-12">
                    {{ $character->officer_note ? $character->officer_note : '—' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">
                        <span class="fas fa-fw fa-lock"></span>
                        Personal Note
                    </span>
                </div>
                <div class="col-12">
                    {{ $character->personal_note ? $character->personal_note : '—' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $(".js-show-next").change(function() {
            showNext(this);
        }).change();

        $(".js-show-next").keyup(function() {
            showNext(this);
        });
    });

    // If the current element has a value, show it and the next element that is hidden because it is empty
    function showNext(currentElement) {
        if ($(currentElement).val() != "") {
            $(currentElement).show();
            $(currentElement).parent().next(".js-hide-empty").show();
        }
    }
</script>
@endsection
