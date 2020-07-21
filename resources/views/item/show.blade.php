@extends('layouts.app')
@section('title', $item->name . ' - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="row mt-3 mb-3">
        <div class="col-12">
            <div class="bg-lightest rounded p-3 d-inline-block item-tooltip"> <!-- d-inline-block to fit the contents rather than screen... I am undecided -->
                <h1 class="font-weight-bold">
                    @if ($itemJson)
                        {{-- %69 (code for 'i') is a workaround that masks the link so wowhead's script won't parse it, allowing *us* to style it however we want --}}
                        <a class="q{!! $itemJson->quality !!}" href="https://classic.wowhead.com/%69tem={{ $item->item_id}}" target="_blank">
                            <span class="iconlarge">
                                <ins style='background-image: url("https://wow.zamimg.com/images/wow/icons/large/{!! $itemJson->icon !!}.jpg");'></ins><del></del></span>{!! $itemJson->name !!}
                        </a>
                    @else
                        @include('partials/item', ['wowheadLink' => true])
                    @endif
                </h1>
                @if ($itemJson)
                    {!! $itemJson->tooltip !!}
                @endif
            </div>
        </div>
    </div>

    <div class="row pt-2 mb-3 bg-lightest rounded">
        <div class="col-12">
            <h2 class="font-weight-bold pl-2">Wishlisted</h2>
        </div>
        <div class="col-12 pr-0 pl-0">
            @if ($wishlistCharacters->count() > 0)
                @include('partials/characterDatatable', ['characters' => $wishlistCharacters])
            @else
                <ul>
                    <li class="lead no-bullet">
                        <em>nobody has added this item to their wishlist yet</em>
                    </li>
                </ul>
            @endif
        </div>
    </div>

    <div class="row mr-1 ml-1 mb-3 pt-1  bg-lightest rounded">
        <div class="col-12">
            <h2>Have It</h2>
            @if ($receivedCharacters->count() > 0)
                <ul class="list-inline striped">
                    @foreach ($receivedCharacters as $character)
                        <li class="list-inline-item rounded pt-2 pl-3 pb-3 pr-3">
                            @include('character/partials/header', ['showDetails' => false, 'showEdit' => false, 'showOwner' => false])
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="lead mb-3">
                    <em>nobody has added this item to their character sheet yet</em>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var characters = {!! $wishlistCharacters->makeVisible('officer_note')->toJson() !!};
    var guild = {!! $guild->toJson() !!};
    var raids = {!! $raids->toJson() !!};
    {{-- TODO PERMISSIONS FOR NOTE --}}
    var showOfficerNote = true;
</script>
<script src="{{ asset('js/roster.js') }}"></script>
@endsection

