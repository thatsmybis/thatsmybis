@extends('layouts.app')
@section('title', 'Loot - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <ul class="list-inline">
                <li class="list-inline-item font-weight-{{ $expansionId == 1 ? 'bold' : 'light' }}">
                    <a href="{{ route('loot.wishlist', ['expansionId' => 'classic']) }}" class="text-{{ getExpansionColor(1) }}">Classic</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item font-weight-{{ $expansionId == 2 ? 'bold' : 'light' }}">
                    <a href="{{ route('loot.wishlist', ['expansionId' => 'tbc']) }}" class="text-{{ getExpansionColor(2) }}">TBC</a>
                </li>
            </ul>
            <h1>
                <span class="font-weight-bold">Top {{ $maxItems }}</span> Wishlisted
                <span class="text-{{ getExpansionColor($expansionId) }} font-weight-bold">{{ $expansionId == 1 ? 'Classic' : 'TBC' }}</span> Items
            </h1>
            <span class="smaller text-muted">Now you can be just like everyone else! :D</span>
        </div>
    </div>
    <div class="row">
        @foreach($wishlists as $key => $wishlist)
            <div class="col-xl-3 col-lg-4 col-sm-6 col-12">
                <div class="bg-light rounded mt-3 mb-3 pt-3 pb-3 col-12">
                    <h2 class="pl-5 font-weight-bold text-{{ strtolower($key) }}">
                        {{ $key }}
                    </h2>
                    <ol>
                        @foreach($wishlist as $item)
                            <li class="margin-bottom-small text-tier-{{ $loop->iteration <= 5 ? '1' : ($loop->iteration <= 10 ? '2' : ($loop->iteration <= 20 ? '3' : ($loop->iteration <= 30 ? '4' : '5'))) }}">
                                @include('partials/item', ['wowheadLink' => false,])
                                <small>
                                    <span class="font-weight-bold text-muted">
                                        {{ $item->count > 0 ? numToKs($item->count) : '' }}
                                    </span>
                                </small>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
