@extends('layouts.app')
@section('title', 'Roster - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="col-12 p-4">
        <div>
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
                    <a class="toggle-column cursor-pointer" data-column="1" href="">Roles</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="2" href="">Rare Recipes</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="3" href="">Alts</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="4" href="">Rank</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="5" href="">Rank Goal</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="6" href="">Wishlist</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="7" href="">Raid Loot</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item">
                    <a class="toggle-column cursor-pointer" data-column="8" href="">Notes</a>
                </li>
                @if (Auth::user()->hasRole('admin|guild_master|officer|raid_leader|raider'))
                    <li class="list-inline-item">&sdot;</li>
                    <li class="list-inline-item">
                        <a class="toggle-column cursor-pointer" data-column="9" href="">Officer Notes</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="mt-4">
            <ul class="list-inline">
                <li class=" list-inline-item">
                    <select id="roleFilter1" class="form-control">
                        <option value="" class="bg-tag">Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" class="bg-tag" style="color:{{ $role->getColor() }};">
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </li>
                <li class=" list-inline-item">
                    <select id="roleFilter2" class="form-control">
                        <option value="" class="bg-tag">Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" class="bg-tag" style="color:{{ $role->getColor() }};">
                                {{ $role->name }}
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
    var members = {!! $members->toJson() !!};
</script>
<script src="{{ asset('js/roster.js') }}"></script>
@endsection
