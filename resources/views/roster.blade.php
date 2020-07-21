@extends('layouts.app')
@section('title',  'Roster - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 mt-2 mb-2">
            <h2 class="font-weight-medium">
                <span class="text-uncommon">
                    &lt;{{ $guild->name }}&gt;
                </span>
                Roster
            </h2>
        </div>
    </div>
    <div class="row pt-2 mb-3 bg-light rounded">
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
