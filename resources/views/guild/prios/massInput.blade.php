@extends('layouts.app')
@section('title', "Prios for " . $raidGroup->name . " " . $instance->name . " - " . config('app.name'))

@php
    // Iterating over 100+ characters 100+ items results in TENS OF THOUSANDS OF ITERATIONS.
    // So we're iterating over the characters only one time, saving the results, and printing them.
    $characterSelectOptions = (string)View::make('partials.characterOptions', ['characters' => $guild->characters]);
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">

            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium font-blizz">
                        <span class="fas fa-fw fa-dungeon text-muted"></span>
                        {{ $raidGroup->name }} Prios for {{ $instance->name }}
                    </h1>
                    <small>
                        <strong>Note:</strong> When someone receives an item, we'll attempt to automatically remove it from their prios. If they have the same item prio'd in multiple raid groups, we'll only remove the first one we find.
                    </small>
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

            <form id="editForm" class="form-horizontal" role="form" method="POST" action="{{ route('guild.prios.massInput.submit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="raid_group_id" value="{{ $raidGroup->id }}">
                <input hidden name="instance_id" value="{{ $instance->id }}">

                <div class="row">
                    <div class="col-12 mt-3 mb-3 bg-light rounded">
                        @php
                            $oldSourceName = null;
                        @endphp
                        @foreach ($items as $item)
                            @if ($item->source_name != $oldSourceName)
                                <div class="row pb-3 pt-4 rounded top-divider">
                                    <h2 class="ml-3 font-weight-medium font-blizz" id="{{ slug($item->source_name) }}">
                                        {{ $item->source_name }}
                                    </h2>
                                </div>
                            @endif
                            <div class="row striped-light pb-2 pt-3 rounded">

                                <input hidden name="items[{{ $item->item_id }}][item_id]" value="{{ $item->item_id }}">

                                <div class="col-lg-4 col-12">
                                    <div class="d-inline-grid align-middle text-5 mb-2">
                                        <label for="items[{{ $item->item_id }}][name]" class="font-weight-bold d-none d-sm-block">
                                            <span class="sr-only">
                                                Item Name
                                            </span>
                                        </label>
                                        @include('partials/item', [
                                            'wowheadLink' => false,
                                            'targetBlank' => true,
                                            'showTier'    => true,
                                            'tierMode'    => $guild->tier_mode,
                                            ])
                                    </div>
                                    @if ($item->childItems->count())
                                        <ul class="ml-3 small list-inline">
                                            @foreach ($item->childItems as $childItem)
                                                <li class="list-inline-item">
                                                    @include('partials/item', [
                                                        'item' => $childItem,
                                                        'iconSize' => 'small',
                                                        'wowheadLink' => false,
                                                        'targetBlank' => true,
                                                    ])
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                <div class="col-lg-5 col-12">
                                    <label for="items[details]" class="font-weight-bold {{ $loop->iteration > 1 ? 'd-none' : '' }}">
                                        @if ($loop->first)
                                            Notes
                                        @else
                                            <span class="sr-only">Notes</span>
                                        @endif
                                    </label>
                                    <ul class="fa-ul">
                                        @if ($item->guild_note)
                                            <li title="Note">
                                                <span class="fa-li"><span class="fal fa-fw fa-sticky-note text-muted"></span></span>
                                                <span class="js-markdown-inline">{{ $item->guild_note }}</span>
                                            </li>
                                        @endif

                                        @if ($item->guild_priority)
                                            <li title="Priority note">
                                                <span class="fa-li"><span class="fal fa-fw fa-sort-amount-down text-muted"></span></span>
                                                <span class="js-markdown-inline">{{ $item->guild_priority }}</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                <div class="col-lg-3 col-12 {{ $errors->has('items.' . $item->item_id . '.*') ? 'bg-danger rounded font-weight-bold' : '' }}">
                                    <div class="form-group mb-2">
                                        <label for="items[{{ $item->item_id }}][characters]" class="font-weight-bold {{ $loop->iteration > 1 ? 'd-none' : '' }}">
                                            @if ($loop->first)
                                                <span class="fas fa-fw fa-sort-amount-down text-muted"></span>
                                                Prio'd Characters
                                                <span class="text-muted font-weight-normal small">max {{ $maxPrios }}</span>
                                            @else
                                                <span class="sr-only">Priority Characters (max {{ $maxPrios }})</span>
                                            @endif
                                        </label>

                                        <select name="" class="js-input-select form-control dark selectpicker" data-live-search="true" autocomplete="off">
                                            <option value="">
                                                —
                                            </option>

                                            {{-- See the notes at the top for why the options look like this --}}
                                            @if (old('items.' . $item->item_id . '.character_id'))
                                                @php
                                                    // Select the correct option
                                                    $options = str_replace('hack="' . old('items.' . $item->item_id . '.character_id') . '"', 'selected', $characterSelectOptions);
                                                 @endphp
                                                 {!! $options !!}
                                            @else
                                                {!! $characterSelectOptions !!}
                                            @endif
                                        </select>

                                        <ol class="js-sortable-lazy no-indent mt-3 mb-0">
                                            {{-- TODO: This is slow; optimize --}}
                                            @for ($i = 0; $i < $maxPrios; $i++)
                                                @php
                                                    $oldInputName   = 'items.' . $item->item_id . '.characters.' . $i;
                                                    $character      = $item->priodCharacters->get($i) ? $item->priodCharacters->get($i) : null;
                                                    $characterId    = old($oldInputName . '.character_id') ? old($oldInputName . '.character_id') : ($character ? $character->id : null);
                                                    $characterLabel = old($oldInputName . '.label') ? old($oldInputName . '.label') : ($character ? $character->name . ' (' . $character->class . ')' : null);
                                                    $characterOrder = old($oldInputName . '.order') ? old($oldInputName . '.order') : ($character ? $character->order : null);
                                                    $strikeThrough  = (!old($oldInputName) && $character) || (old($oldInputName) && $character && old($oldInputName . 'character_id') == $character->id) ? ($character->pivot->received_at ? 'font-strikethrough' : null) : null;
                                                    // $isReceived     = old($oldInputName . '.is_received') && old($oldInputName . '.is_received') == 1 ? 'checked' : ($character && $character->pivot->is_received ? 'checked' : null);
                                                    // $isOffspec      = old($oldInputName . '.is_offspec') && old($oldInputName . '.is_offspec') == 1 ? 'checked' : ($character && $character->pivot->is_offspec ? 'checked' : null);
                                                @endphp
                                                <li class="input-item position-relative {{ $characterId ? 'd-flex' : '' }} {{ $errors->has('items.' . $item->item_id . '.characters.' . $i ) ? 'text-danger font-weight-bold' : '' }} {{ $strikeThrough }}"
                                                    style="{{ $characterId ? '' : 'display:none;' }}">

                                                    <input type="checkbox" checked name="items[{{ $item->item_id }}][characters][{{ $i }}][character_id]" value="{{ $characterId }}" style="display:none;">
                                                    <input type="checkbox" checked name="items[{{ $item->item_id }}][characters][{{ $i }}][label]" value="{{ $characterLabel }}" style="display:none;">

                                                    <button type="button" class="js-input-button close close-top-right text-unselectable" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>

                                                    <div class="js-sort-handle js-input-label d-flex move-cursor text-unselectable mr-1 text-4">
                                                        <div class="justify-content-center align-self-center">
                                                            <span class="fas fa-fw fa-grip-vertical text-muted"></span>
                                                        </div>
                                                    </div>

                                                    <div class="js-input-label">
                                                        <span class="text-{{ $character ? strtolower($character->class) : '' }} font-weight-medium">
                                                            {!! $characterLabel !!}
                                                        </span>

                                                        <ul class="list-inline">
                                                            <li class="list-inline-item">
                                                                <div class="form-inline">
                                                                    <div class="form-group">
                                                                        <label for="items[{{ $item->item_id }}][characters][{{ $i }}][order]">
                                                                            Rank
                                                                        </label>
                                                                        <input name="items[{{ $item->item_id }}][characters][{{ $i }}][order]"
                                                                            type="number"
                                                                            min="0"
                                                                            max="{{ $maxPrios }}"
                                                                            class="form-control dark"
                                                                            placeholder="auto"
                                                                            autocomplete="off"
                                                                            style="width:85px;"
                                                                            value="{{ $characterOrder }}" />
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            {{--
                                                            Removed until I can get some optimizations added to this page... can't just have maxInputs*items*fields... it's up to like 10k inputs
                                                            Need to add these fields dynamically; as needed server-side, and then as-needed client-side
                                                            <li class="list-inline-item">
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="items[{{ $item->item_id }}][characters][{{ $i }}][is_received]" value="1" class="" autocomplete="off"
                                                                            {{ $isReceived }}>
                                                                            Received
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li class="list-inline-item">
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="items[{{ $item->item_id }}][characters][{{ $i }}][is_offspec]" value="1" class="" autocomplete="off"
                                                                            {{ $isOffspec }}>
                                                                            OS
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            --}}
                                                        </ul>


                                                    </div>
                                                </li>

                                                @if ($errors->has('items.' . $item->item_id . '.characters.*'))
                                                    <li class="'text-danger font-weight-bold'">
                                                        {{ $errors->first('items.' . $item->item_id . '.characters.*') }}
                                                    </li>
                                                @endif
                                            @endfor
                                        </ol>

                                        @if ($errors->has('items.' . $item->item_id . '.*'))
                                            <div class="'text-danger font-weight-bold'">
                                                {{ $errors->first('items.' . $item->item_id . '.*') }}
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
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Submit</button>
                    <br>
                    <small>WARNING: This form expires if you don't submit it within {{ env('SESSION_LIFETIME') / 60 }} hours (security reasons)
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ loadScript('autocomplete.js') }}"></script>
<script>
    $(document).ready(() => warnBeforeLeaving("#editForm"));
</script>
@endsection

@section('wowheadIconSize', 'medium')
