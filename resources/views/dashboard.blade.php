@extends('layouts.app')
@section('title', 'Dashboard - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row"><div class="col-12">
            <ul class="text-center no-bullet">
                <li>
                    <h2><a href="{{ route('calendar') }}" class="">Calendar</a></h2>
                </li>
                <li>
                    <h2><a href="{{ route('roster') }}" class="">Roster</a></h2>
                </li>
                <li>
                    <h2><a href="{{ route('recipes') }}" class="">Recipes</a></h2>
                </li>
                <li>
                    <h2>Resources</h2>
                </li>
                <li>
                    <a href="{{ route('pvpResources') }}" class="font-weight-bold">PvP</a>
                </li>
                <li>
                    <a href="{{ route('pveResources') }}" class="font-weight-bold">PvE</a>
                </li>
                <li>
                    <a href="{{ route('druidResources') }}" class="text-druid font-weight-bold">Druid</a>
                </li>
                <li>
                    <a href="{{ route('hunterResources') }}" class="text-hunter font-weight-bold">Hunter</a>
                </li>
                <li>
                    <a href="{{ route('mageResources') }}" class="text-mage font-weight-bold">Mage</a>
                </li>
                <li>
                    <a href="{{ route('priestResources') }}" class="text-priest font-weight-bold">Priest</a>
                </li>
                <li>
                    <a href="{{ route('rogueResources') }}" class="text-rogue font-weight-bold">Rogue</a>
                </li>
                <li>
                    <a href="{{ route('shamanResources') }}" class="text-shaman font-weight-bold">Shaman</a>
                </li>
                <li>
                    <a href="{{ route('warlockResources') }}" class="text-warlock font-weight-bold">Warlock</a>
                </li>
                <li>
                    <a href="{{ route('warriorResources') }}" class="text-warrior font-weight-bold">Warrior</a>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection
