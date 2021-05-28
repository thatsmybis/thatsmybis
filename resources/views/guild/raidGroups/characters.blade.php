@extends('layouts.app')
@section('title', $raidGroup->name . ' Mains - ' . config('app.name'))

@section('content')
{{ $raidGroup->secondary_characters_count }} other char{{ $raidGroup->secondary_characters_count != 1 ? 's' : '' }}
@foreach ($raidGroup->characters as $character)
    {{ $character->name }}
@endforeach

@endsection
