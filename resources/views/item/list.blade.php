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
                <p class="font-weight-bold text-gold">
                    {{ __("To assign Prios and Wishlists sign in and register your guild.") }}
                </p>
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
    var items            = {!! $items ? $items->toJson() : '{}' !!};
    var maxWishlistLists = {{ App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS }};
    var raidGroups       = {!! $raidGroups ? $raidGroups->toJson() : '{}' !!};
    var showNotes        = {{ $showNotes ? 'true' : 'false' }};
    var showOfficerNote  = {{ $showOfficerNote ? 'true' : 'false' }};
    var showPrios        = {{ $showPrios ? 'true' : 'false' }};
    var showWishlist     = {{ $showWishlist ? 'true' : 'false' }};
</script>
<script src="{{ loadScript('itemList.js') }}"></script>
@endsection
