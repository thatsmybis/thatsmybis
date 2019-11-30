@extends('layouts.app')
@section('title', 'Roster - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="col-12 p-4">
        <ul class="list-inline">
            <li>
                <strong>Columns</strong>
            </li>
            <li class="list-inline-item">
                <a class="toggle-column-default cursor-pointer font-weight-bold" href="">Default</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column-loot-council cursor-pointer font-weight-bold" href="">Loot Council</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column-bare cursor-pointer font-weight-bold" href="">Min</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
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
                <a class="toggle-column cursor-pointer" data-column="8" href="">Raid</a>
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
                <a class="toggle-column cursor-pointer" data-column="9" href="">Wishlist</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="10" href="">Raid Loot</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="11" href="">Notes</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <a class="toggle-column cursor-pointer" data-column="12" href="">Officer Notes</a>
            </li>
        </ul>
        <ul class="list-inline">
            <li class=" list-inline-item">
                <select id="classFilter" class="form-control"><option value="">Class</option></select>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <select id="raidFilter" class="form-control"><option value="">Raid</option></select>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <select id="professionFilter" class="form-control"><option value="">Profession</option></select>
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
