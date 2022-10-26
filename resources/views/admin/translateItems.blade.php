@extends('layouts.app')
@section('title', 'Translate Item Names - ' . config('app.name'))

@section('wowheadShowIcons', 'false')

@section('content')

    <ul>
        <li>
            <a href="{{ route('admin.translateItems', ['expansion' => $expansion, 'lang' => $lang, 'minId' => $minId, 'maxId' => $maxId]) }}">get link for editing</a>
            <small>(click this, then edit URL to your needs)</small>
        </li>
        <li>
            expansion: <strong>{{ $expansion }}</strong>
            <small>(classic, tbc, wotlk)</small>
        </li>
        <li>
            lang: <strong>{{ $lang }}</strong>
            <small>(cn, de, es, fr, it, ko, pt, ru)</small>
        </li>
        <li>
            minId: <strong>{{ $minId }}</strong>
            <small>(first item to show)</small>
        </li>
        <li>
            maxId: <strong>{{ $maxId }}</strong>
            <small>(last item to show)</small>
        </li>
    </ul>
    <ul>
        @foreach ($items as $item)
            <li>
                {{ $item->item_id }} @include('partials/item', ['itemExpansionId' => $expansionId, 'wowheadLocale' => $lang, 'wowheadLink' => true])
            </li>
        @endforeach
    </ul>
@endsection
