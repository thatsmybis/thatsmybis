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
    var raids      = {!! $raids->toJson() !!};
    var showOfficerNote = {{ $showOfficerNote ? 'true' : 'false' }};
</script>
<script src="{{ env('APP_ENV') == 'local' ? asset('/js/roster.js') : mix('js/processed/roster.js') }}"></script>
@endsection
