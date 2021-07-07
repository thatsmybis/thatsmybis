@extends('layouts.app')
@section('title', getExpansionAbbr($expansionId) . ' ' . __('Wishlists') . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <ul class="list-inline">
                <li class="list-inline-item font-weight-{{ $expansionId == 1 ? 'bold' : 'light' }}">
                    <a href="{{ route('loot.wishlist', ['expansionId' => 'classic']) }}" class="text-{{ getExpansionColor(1) }}">{{ __("Classic") }}</a>
                </li>
                <li class="list-inline-item">&sdot;</li>
                <li class="list-inline-item font-weight-{{ $expansionId == 2 ? 'bold' : 'light' }}">
                    <a href="{{ route('loot.wishlist', ['expansionId' => 'tbc']) }}" class="text-{{ getExpansionColor(2) }}">{{ __("TBC") }}</a>
                </li>
            </ul>
            <h1>
                <span class="font-weight-bold">{{ __("Top") }} {{ $maxItems }}</span> {{ __("Wishlisted") }}
                <span class="text-{{ getExpansionColor($expansionId) }} font-weight-bold">{{ getExpansionAbbr($expansionId) }}</span> {{ __("Items") }}
            </h1>
            <span class="smaller text-muted">{{ __("Now you can be just like everyone else!") }} :D</span>
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
                            <li class="mb-2 text-tier-{{ $loop->iteration <= 5 ? '1' : ($loop->iteration <= 10 ? '2' : ($loop->iteration <= 20 ? '3' : ($loop->iteration <= 30 ? '4' : '5'))) }}">
                                @include('partials/item', ['wowheadLink' => false,])
                                <small class="text-muted">
                                    <span class="font-weight-bold">
                                        {{ $item->count > 0 ? numToKs($item->count) : '' }}
                                    </span>
                                    <em>
                                        {{ $item->instance_name }}
                                    </em>
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
