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
        </div>
        <div class="col-12 pr-0 pl-0">
            @include('partials/itemDatatable')
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var items = {!! $items->toJson() !!};
    var guild = {!! $guild->toJson() !!};
    var raids = {!! $raids->toJson() !!};
    var showOfficerNote = {{ $showOfficerNote ? 'true' : 'false' }};
    var showPrios       = {{ $showPrios ? 'true' : 'false' }};
    var showWishlist    = {{ $showWishlist ? 'true' : 'false' }};
</script>
<script src="{{ env('APP_ENV') == 'local' ? asset('/js/itemList.js') : mix('js/processed/itemList.js') }}"></script>
@endsection
