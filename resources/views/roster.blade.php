@extends('layouts.app')
@section('title', 'Roster - ' . config('app.name'))

@section('content')

<div class="container-fluid pr-0 pl-0">
    <div class="col-12 pb-2 pt-2 pr-0 pl-0">
        @include('partials/characterDatatable')
    </div>
</div>

@endsection

@section('scripts')
<script>
    var characters = {!! $characters->makeVisible('officer_note')->toJson() !!};
    var guild = {!! $guild->toJson() !!};
    var raids = {!! $raids->toJson() !!};
    {{-- TODO PERMISSIONS FOR NOTE --}}
    var showOfficerNote = true;
</script>
<script src="{{ asset('js/roster.js') }}"></script>
@endsection
