@extends('layouts.app')
@section('title', $raidGroup->name . ' Other Chars - ' . config('app.name'))

@section('content')
{{ $raidGroup->characters_count }} main char{{ $raidGroup->characters_count != 1 ? 's' : '' }}
@foreach ($raidGroup->secondaryCharacters as $character)
    {{ $character->name }}
@endforeach

@endsection
