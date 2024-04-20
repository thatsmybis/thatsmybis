@extends('layouts.app')
@section('title',  __("Roster Stats") . ' - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 pr-0 pl-0">
            @include('partials/characterStatsDatatable', ['receivedLootDateFilter' => true])
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var characters       = {!! $characters !!};
    var currentWishlistNumber = {{ $guild->current_wishlist_number }};
    var guild            = {!! $guild->toJson() !!};
    var maxWishlistLists = {{ App\Http\Controllers\CharacterLootController::MAX_WISHLIST_LISTS }};
    var raidGroups       = {!! $raidGroups->toJson() !!};
    var showEdit         = {{ $showEdit ? 'true' : 'false' }};
    var showOfficerNote  = {{ $showOfficerNote ? 'true' : 'false' }};
    var showPrios        = {{ $showPrios ? 'true' : 'false' }};
    var showWishlist     = {{ $showWishlist ? 'true' : 'false' }};
    var wishlistNames    = {!! $guild->getWishlistNames() ? json_encode($guild->getWishlistNames()) : 'null' !!};
</script>
<script src="{{ loadScript('rosterStats.js') }}"></script>
@endsection

@section('wowheadIconSize', 'tiny')
