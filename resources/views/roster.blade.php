@extends('layouts.app')
@section('title',  $guild->name . ' Roster - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row pt-2 mb-3 bg-lightest rounded">
        <div class="col-12 mt-2 mb-2">
            <h1 class="font-weight-bold">
                <span class="text-success">
                    &lt;{{ $guild->name }}&gt;
                </span>
                Roster
            </h1>
        </div>
        <div class="col-12 pr-0 pl-0">
            @include('partials/characterDatatable')
        </div>
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
