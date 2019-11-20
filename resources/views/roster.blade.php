@extends('layouts.app')
@section('title', 'Roster - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="col-12 p-4">
        <ul class="list-inline">
            <li>
                <strong>Column Toggles</strong>
            </li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="0" href="">Name</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="1" href="">Class</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="2" href="">Spec</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="3" href="">Professions</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="4" href="">Rare Recipes</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="5" href="">Alts</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="6" href="">Rank</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="7" href="">Rank Goal</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="8" href="">Wishlist</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="9" href="">Raid Loot</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="10" href="">Notes</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="11" href="">Officer Notes</a>
            </li>
        </ul>
    </div>
    <div class="col-12 p-3 bg-light rounded">
        <table id="roster" class="col-xs-12 table table-border table-hover stripe">
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
