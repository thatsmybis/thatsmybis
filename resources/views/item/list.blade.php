@extends('layouts.app')
@section('title',  $instance->name . ' - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 text-center pr-0 pl-0">
            <h1 class="font-weight-medium mb-0 font-blizz">
                <span class="fas fa-fw fa-sack text-success"></span>
                {{ $instance->name }}
            </h1>
            @if (!$guild)
                @include('partials/firstHitIsFree')
            @elseif ($viewPrioPermission || $viewOfficerNotesPermission)
                <ul class="list-inline">
                    @if ($viewOfficerNotesPermission)
                        <li class="list-inline-item">
                            <a href="{{ route('guild.item.list.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug]) }}">{{ __("edit notes") }}</a>
                        </li>
                    @endif
                    @if ($viewPrioPermission)
                        <li class="list-inline-item">&sdot;</li>
                        <li class="list-inline-item">
                            <a href="{{ route('guild.prios.chooseRaidGroup', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug]) }}">{{ __("edit prios") }}</a>
                        </li>
                    @endif
                </ul>
            @endif
            @if ($instance->itemSources)
                <ul class="list-inline">
                    @foreach ($instance->itemSources as $itemSource)
                        @if (!$loop->first)
                            <li class="list-inline-item">
                                &sdot;
                            </li>
                        @endif
                        <li class="list-inline-item">
                            <a href="#{{ trim($itemSource->slug) }}">{{ $itemSource->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="col-12 pr-0 pl-0">
            @include('partials/itemDatatable')
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var currentWishlistNumber = {{ $guild ? $guild->current_wishlist_number : 'null' }};
    var guild            = {!! $guild ? $guild->toJson() : '{}' !!};
    // Used for checking attendance
    var guildCharacters  = {!! $charactersWithAttendance ? $charactersWithAttendance->toJson() : '{}' !!};
    var items            = {!! $items ? $items : '{}' !!};
    var maxWishlistLists = {{ App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS }};
    var raidGroups       = {!! $raidGroups ? $raidGroups->toJson() : '{}' !!};
    var showNotes        = {{ $showNotes ? 'true' : 'false' }};
    var showOfficerNote  = {{ $showOfficerNote ? 'true' : 'false' }};
    var showPrios        = {{ $showPrios ? 'true' : 'false' }};
    var showWishlist     = {{ $showWishlist ? 'true' : 'false' }};
    var wishlistNames    = {!! $guild ? ($guild->getWishlistNames() ? json_encode($guild->getWishlistNames()) : 'null') : 'null' !!};
</script>
<script src="{{ loadScript('itemList.js') }}"></script>
@endsection
