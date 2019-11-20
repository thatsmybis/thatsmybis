@extends('layouts.app')
@section('title', 'Roster - ' . config('app.name'))

@section('content')

<div class="container-fluid bg-light p-5">
    <div class="col-xs-12">
        <table id="roster" class="col-xs-12 table table-border table-hover">
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var members = {!! $members->toJson() !!};
</script>
<script src="{{ asset('js/roster.js') }}"></script>
@endsection
