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
        <div class="col-12 pt-2 mb-2 text-center">
            <ul class="list-inline mb-0">
                @foreach ($classes as $class)
                    @if (!$loop->first)
                        <li class="list-inline-item">
                            &sdot;
                        </li>
                    @endif
                    <li class="list-inline-item mt-2 mb-2 font-weight-bold">
                        <a href="#{{ strtolower($class) }}" class="text-{{ strtolower($class) }}">
                            {{ $class }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="row">
        @foreach ($classes as $class)
            <div class="col-12 p-0">
                <div class="mb-3 mr-3 ml-3 pt-3 pr-3 pl-3 bg-light rounded">
                    <h1 id="{{ strtolower($class) }}"
                        class="text-center text-{{ strtolower($class) }} font-weight-bold mt-3">
                        {{ $class }}
                    </h1>
                    <div class="row">
                        @foreach ($specsWithItems->where('class', $class) as $spec)
                            @php
                                $archetypeSum = $spec->archetypes->sum();
                            @endphp
                            <div class="pt-3 pb-3 col-lg-4 col-md-6 col-12">
                                <h3 class="text-{{ strtolower($spec->class) }} font-weight-medium">
                                    <div class="class-icon medium inline"
                                        style="background-image: url(&quot;{{ asset('images/' . $spec->icon) }}&quot;);"></div>
                                    {{ $spec->name }}
                                </h3>
                                @if ($archetypeSum)
                                    @php
                                        $showArchetype = false;
                                    @endphp
                                    <ul class="nav nav-tabs mb-3" id="{{ strtolower($spec->class) }}Tab">
                                        @foreach ($archetypes as $archetype)
                                            @php
                                                // Any given archetype for a spec surpass 10% of all specs to be counted
                                                // OR be the predefined archetype for this spec and have at least one item.
                                                if (
                                                    $spec->archetypes[strtolower($archetype)] > ($archetypeSum * 0.1)
                                                    || $spec->archetypes[strtolower($archetype)] && $spec->archetype == $archetype
                                                ) {
                                                    $showArchetype = true;
                                                }
                                            @endphp
                                            @if ($showArchetype && $spec->archetypes[strtolower($archetype)] > ($archetypeSum * 0.1))
                                                <li class="nav-item">
                                                    <a class="nav-link {{ $spec->archetype == $archetype ? 'active' : '' }} text-{{ strtolower($class) }}"
                                                        id="{{ strtolower($spec->class) }}-{{ strtolower($spec->name) }}-{{ strtolower($archetype) }}-tab"
                                                        href="#{{ strtolower($spec->class) . ucfirst(strtolower($spec->name)) . ucfirst(strtolower($archetype)) }}"
                                                        data-toggle="tab"
                                                        role="tab"
                                                        aria-controls="{{ strtolower($archetype) }}"
                                                        aria-selected="{{ $spec->archetype == $archetype ? 'true' : 'false' }}">
                                                        {{ $archetype }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                    <div class="tab-content" id="{{ strtolower($spec->class) . ucfirst(strtolower($archetype)) }}TabContent">
                                        @foreach ($archetypes as $archetype)
                                            @if ($showArchetype)
                                                <div class="tab-pane fade {{ $spec->archetype == $archetype ? 'show active' : '' }}"
                                                    id="{{ strtolower($spec->class) . ucfirst(strtolower($spec->name)) . ucfirst(strtolower($archetype)) }}"
                                                    role="tabpanel"
                                                    aria-labelledby="{{ strtolower($spec->class) }}-{{ strtolower($spec->name) }}-{{ strtolower($archetype) }}-tab">
                                                    <ol>
                                                        @foreach ($spec->items->where('archetype', $archetype) as $item)
                                                            <li class="mb-2 text-tier-{{ $loop->iteration <= 5 ? '1' : ($loop->iteration <= 10 ? '2' : ($loop->iteration <= 20 ? '3' : ($loop->iteration <= 30 ? '4' : '5'))) }}">
                                                                @include('partials/item', ['wowheadLink' => false])
                                                                <small class="text-muted">
                                                                    <span class="font-weight-bold text-{{ strtolower($class) }}">
                                                                        {{ $item->wishlist_count > 0 ? numToKs($item->wishlist_count) : '' }}
                                                                    </span>
                                                                    <em>
                                                                        {{ $item->instance_short_name }}
                                                                    </em>
                                                                </small>
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="mt-3 ml-3">
                                        {{ __('No data yet') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
