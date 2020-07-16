@extends('layouts.app')
@section('title', 'Roster - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="col-12 p-4">
        <div>
            <ul class="list-inline">
                <li>
                    <strong>Column Visibility</strong>
                </li>
                <li class="list-inline-item">
                    <a class="toggle-column-default cursor-pointer font-weight-bold" href="">Default</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="1" href="">Loot Received</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="2" href="">Wishlist</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="3" href="">Recipes</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="4" href="">Roles</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="5" href="">Notes</a>
                </li>
            </ul>
        </div>
        <div class="mt-4">
            <ul class="list-inline">
                <li class=" list-inline-item">
                    <label for="raid_filter">
                        Raid
                    </label>
                    <select id="raid_filter" class="form-control">
                        <option value="" class="bg-tag">—</option>
                        @foreach ($raids as $raid)
                            <option value="{{ $raid->name }}" class="bg-tag" style="color:{{ $raid->getColor() }};">
                                {{ $raid->name }}
                            </option>
                        @endforeach
                    </select>
                </li>
                <li class=" list-inline-item">
                    <label for="class_filter">
                        Class
                    </label>
                    <select id="class_filter" class="form-control">
                        <option value="" class="bg-tag">—</option>
                        @foreach (App\Character::classes() as $class)
                            <option value="{{ $class }}" class="bg-tag text-{{ strtolower($class) }}">
                                {{ $class }}
                            </option>
                        @endforeach
                    </select>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-12 p-3 rounded">
        <table id="roster" class="col-xs-12 table table-border table-hover stripe">
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var characters = {!! $characters->toJson() !!};
    var raids = {!! $raids->toJson() !!};
    {{-- TODO PERMISSIONS FOR NOTE --}}
    var showOfficerNote = true;
</script>
<script src="{{ asset('js/roster.js') }}"></script>
@endsection
