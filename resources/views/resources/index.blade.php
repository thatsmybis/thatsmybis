@extends('layouts.app')
@section('title', 'Resources - ' . config('app.name'))

@section('content')

<ul>
    <li>
        <a href="{{ route('resources') }}">Index</a>
    </li>
    <li>
        <a href="{{ route('pvpResources') }}">PvP</a>
    </li>
    <li>
        <a href="{{ route('pveResources') }}">PvE</a>
    </li>
    <li>
        <a href="{{ route('druidResources') }}" class="text-druid">Druid</a>
    </li>
    <li>
        <a href="{{ route('hunterResources') }}" class="text-hunter">Hunter</a>
    </li>
    <li>
        <a href="{{ route('mageResources') }}" class="text-mage">Mage</a>
    </li>
    <li>
        <a href="{{ route('priestResources') }}" class="text-priest">Priest</a>
    </li>
    <li>
        <a href="{{ route('rogueResources') }}" class="text-rogue">Rogue</a>
    </li>
    <li>
        <a href="{{ route('shamanResources') }}" class="text-shaman">Shaman</a>
    </li>
    <li>
        <a href="{{ route('warlockResources') }}" class="text-warlock">Warlock</a>
    </li>
    <li>
        <a href="{{ route('warriorResources') }}" class="text-warrior">Warrior</a>
    </li>
</ul>

@endsection
