@extends('layouts.app')
@section('title',  'Roster - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 pr-0 pl-0">
            @include('partials/characterDatatable')
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var characters = {!! $characters->makeVisible('officer_note')->toJson() !!};
    var guild      = {!! $guild->toJson() !!};
    var raidGroups = {!! $raidGroups->toJson() !!};
    var showEdit        = {{ $showEdit ? 'true' : 'false' }};
    var showOfficerNote = {{ $showOfficerNote ? 'true' : 'false' }};
    var showPrios       = {{ $showPrios ? 'true' : 'false' }};
    var showWishlist    = {{ $showWishlist ? 'true' : 'false' }};
</script>
<script src="{{ loadScript('roster.js') }}"></script>
@endsection
