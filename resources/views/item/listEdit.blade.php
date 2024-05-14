@extends('layouts.app')
@section('title', $instance-> name . " " . __("Notes") . " - " . config('app.name'))

@php
    // Iterating over 6+ tiers 100+ items results in a lot of needless iterations.
    // So we're just doing it once, saving the results, and printing them.
    $tierSelectOptions = (string)View::make('partials.tierOptions', ['tiers' => $guild->tiers(), 'tierMode' => $guild->tier_mode]);
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12 {{ request()->get('hideAds') ? '' : 'col-xl-10' }}">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium font-blizz">
                        <span class="fas fa-fw fa-dungeon text-muted"></span>
                        {{ $instance->name }}
                    </h1>
                </div>
            </div>

            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <ul class="list-inline mb-0">
                        @foreach ($instance->itemSources as $itemSource)
                            @if (!$loop->first)
                                <li class="list-inline-item">
                                    &sdot;
                                </li>
                            @endif
                            <li class="list-inline-item mt-2 mb-2">
                                <a href="#{{ $itemSource->slug }}">
                                    {{ $itemSource->name }}
                                </a>
                            </li>
                        @endforeach
                        <li class="list-inline-item mt-2 mb-2">
                            <span id="loadAverageTiers" class="btn btn-secondary">
                                <span class="fas fa-fw fa-trophy"></span>
                                Load Default Tiers
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            @if (count($errors) > 0)
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <form id="editForm" class="form-horizontal" role="form" method="POST" action="{{ route('guild.item.list.submit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug]) }}">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-12 mt-3 mb-3 bg-light rounded">
                        @php
                            $oldSourceName = null;
                            $headerCount = 0;
                        @endphp
                        @foreach ($items as $item)
                            @if ($item->source_name != $oldSourceName)
                                @php
                                    $headerCount++;
                                @endphp
                                <div class="row pb-3 pt-4 rounded top-divider">
                                    <div class="col-12">
                                        <h2 class="font-weight-medium font-blizz" id="{{ slug($item->source_name) }}">
                                            {{ $item->source_name }}
                                        </h2>
                                    </div>
                                </div>
                            @endif

                            <input hidden name="items[{{ $loop->iteration }}][id]" value="{{ $item->item_id }}" />

                            <div class="row striped-light pb-2 pt-3 rounded">

                                <div class="col-lg-{{ $guild->tier_mode ? '3' : '4' }} col-12">
                                    <div class="d-inline-grid align-middle text-5 mb-2">
                                        <label for="items[{{ $loop->iteration }}][name]" class="font-weight-bold  {{ !$loop->first ? 'd-none' : 'd-none d-sm-block' }}">
                                            <span class="sr-only">
                                                {{ __("Item Name") }}
                                            </span>
                                        </label>
                                        @include('partials/item', [
                                            'wowheadLink' => false,
                                            'targetBlank' => true,
                                            ])
                                    </div>
                                    @if ($item->childItems->count())
                                        <ul class="ml-1 small list-inline">
                                            @foreach ($item->childItems as $index => $childItem)
                                                <li class="list-inline-item {{ $index > 10 ? 'd-none' : '' }}">
                                                    @include('partials/item', [
                                                        'item' => $childItem,
                                                        'iconSize' => 'small',
                                                        'wowheadLink' => false,
                                                        'targetBlank' => true,
                                                    ])
                                                </li>
                                            @endforeach
                                            @if ($item->childItems->count() > 10)
                                                <li>
                                                    <a href="{{ route('guild.item.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'slug' => slug($item->name)]) }}" target="_blank">
                                                        {{ $item->childItems->count() - 10 }} more…
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                </div>

                                <div class="col-lg-1 col-12 {{ $errors->has('items.' . $loop->iteration . '.tier') ? 'bg-danger rounded font-weight-bold' : '' }}" style="{{ $guild->tier_mode ? '' : 'display:none;' }}">
                                    <div class="form-group">

                                        <label for="items[{{ $loop->iteration }}][tier]" class="font-weight-bold  {{ !$loop->first ? 'd-none' : '' }}">
                                            @if ($loop->first)
                                                <span class="fas fa-fw fa-trophy text-muted"></span>
                                                {{ __("Tier") }}
                                            @else
                                                <span class="sr-only">{{ __("Tier") }}</span>
                                            @endif
                                        </label>

                                        <select name="items[{{ $loop->iteration }}][tier]" data-item-id="{{ $item->item_id }}" class="form-control dark">
                                            <option value="" selected>
                                            —
                                            </option>
                                            {{-- See the notes at the top for why the options look like this --}}
                                            @if (old('items.' . $loop->iteration . '.tier') || $item->guild_tier)
                                                @php
                                                    $tier = old('items.' . $loop->iteration . '.tier') ? old('items.' . $loop->iteration . '.tier') : $item->guild_tier;
                                                    // Select the correct option
                                                    $options = str_replace('hack="' . $tier . '"', 'selected', $tierSelectOptions);
                                                 @endphp
                                                 {!! $options !!} {{$tier }}
                                            @else
                                                {!! $tierSelectOptions !!}
                                            @endif

                                        </select>

                                        @if ($averageTiers[$item->item_id]->average_tier)
                                            <span class="font-weight-light text-muted small">
                                                {{ __("avg.") }}
                                                <span class="text-tier-">
                                                    @if ($guild->tier_mode == App\Guild::TIER_MODE_S)
                                                        {{ numToSTier(number_format($averageTiers[$item->item_id]->average_tier, 1)) }}
                                                    @else
                                                        {{ number_format($averageTiers[$item->item_id]->average_tier, 1) }}
                                                    @endif
                                                </span>
                                            </span>
                                        @endif

                                        @if ($errors->has('items.' . $loop->iteration . '.tier'))
                                            <div class="'text-danger font-weight-bold'">
                                                {{ $errors->first('items.' . $loop->iteration . '.tier') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-lg-4 col-12">
                                    <div class="form-group {{ $errors->has('items.' . $loop->iteration . '.note') || $errors->has('items.' . $loop->iteration . '.id') ? 'bg-danger rounded font-weight-bold' : '' }}">
                                        <label for="items[{{ $loop->iteration }}][note]" class="font-weight-bold  {{ !$loop->first ? 'd-none' : '' }}">
                                            @if ($loop->first)
                                                <span class="fas fa-fw fa-sticky-note text-muted"></span>
                                                {{ __("Note") }}
                                            @else
                                                <span class="sr-only">{{ __("Note") }}</span>
                                            @endif
                                        </label>

                                        <textarea name="items[{{ $loop->iteration }}][note]"
                                            maxlength="2000"
                                            data-max-length="2000"
                                            rows="4"
                                            type="text"
                                            placeholder="{{ __('add a note') }}"
                                            class="form-control dark"
                                        >{{ old('items.' . $loop->iteration . '.note') ? old('items.' . $loop->iteration . '.note') : ($item->guild_note ? $item->guild_note : '') }}</textarea>

                                        @if ($errors->has('items.' . $loop->iteration . '.note'))
                                            <div class="'text-danger font-weight-bold'">
                                                {{ $errors->first('items.' . $loop->iteration . '.note') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group {{ $errors->has('items.' . $loop->iteration . '.officer_note') ? 'bg-danger rounded font-weight-bold' : '' }}">
                                        <label for="officer_note" class="font-weight-bold {{ !$loop->first ? 'd-none' : '' }}">
                                            @if ($loop->first)
                                                <span class="fas fa-fw fa-shield text-muted"></span>
                                                {{ __("Officer Note") }}
                                            @else
                                                <span class="sr-only">{{ __("Officer Note") }}</span>
                                            @endif
                                        </label>

                                        <textarea name="items[{{ $loop->iteration }}][officer_note]"
                                            maxlength="2000"
                                            data-max-length="2000"
                                            rows="4"
                                            type="text"
                                            placeholder="{{ __('add an officer note') }}"
                                            class="form-control dark
                                        {{ $isStreamerMode ? 'd-none' : '' }}">{{ old('items.' . $loop->iteration . '.officer_note') ? old('items.' . $loop->iteration . '.officer_note') : ($item->guild_officer_note ? $item->guild_officer_note : '') }}</textarea>

                                        @if ($isStreamerMode)
                                            <span class="text-muted small">{{ __("officer note hidden in streamer mode") }}</span>
                                        @endif

                                        @if ($errors->has('items.' . $loop->iteration . '.officer_note'))
                                            <div class="'text-danger font-weight-bold'">
                                                {{ $errors->first('items.' . $loop->iteration . '.officer_note') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-lg-4 col-12 {{ $errors->has('items.' . $loop->iteration . '.priority') ? 'bg-danger rounded font-weight-bold' : '' }}">
                                    <div class="form-group">

                                        <label for="priority" class="font-weight-bold  {{ !$loop->first ? 'd-none' : '' }}">
                                            @if ($loop->first)
                                                <span class="fas fa-fw fa-sort-amount-down text-muted"></span>
                                                {{ __("Priority") }}
                                            @else
                                                <span class="sr-only">{{ __("Priority") }}</span>
                                            @endif
                                        </label>

                                        <textarea name="items[{{ $loop->iteration }}][priority]"
                                            maxlength="2000"
                                            data-max-length="2000"
                                            rows="4"
                                            type="text"
                                            placeholder="{{ __('eg. mage > warlock > boomkin') }}
{{ __('NOTE: Gargul addon displays each `>` character as a numbered list item on a new line') }}"
                                            class="form-control dark"
                                        >{{ old('items.' . $loop->iteration . '.priority') ? old('items.' . $loop->iteration . '.priority') : ($item->guild_priority ? $item->guild_priority : '') }}</textarea>

                                        @if ($errors->has('items.' . $loop->iteration . '.priority'))
                                            <div class="'text-danger font-weight-bold'">
                                                {{ $errors->first('items.' . $loop->iteration . '.priority') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @php
                                $oldSourceName = $item->source_name;
                            @endphp
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Submit") }}</button>
                    <br>
                    <small>{{ __("WARNING: This form expires if you don't submit it within :hours hours (security reasons)", ['hours' =>  env('SESSION_LIFETIME') / 60]) }}</small>
                </div>
            </form>
        </div>
        @if (!request()->get('hideAds'))
            <div class="d-none col-xl-2 d-xl-block m-0 p-0">
                @include('partials/adRightBanner')
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    var averageTiers = {!! $averageTiers->toJson() !!};
    $(document).ready(function () {
        warnBeforeLeaving("#editForm");

        $("#loadAverageTiers").click(function () {
            if (confirm("{{ __("Doing this will overwrite any custom tiers for this dungeon. Continue?") }} {{ __("note: Only works if other guilds have added tiers that can be used as a baseline.") }}")) {
                $.each($("select"), function (key, value) {
                    let itemId = $(this).data("itemId");
                    if (itemId) {
                        let averageTier = averageTiers[itemId].average_tier;
                        if (averageTier) {
                            $(this).val(Math.round(averageTier));
                        }
                    }
                });
            }
        });
    });
</script>
@endsection

@section('wowheadIconSize', 'medium')
