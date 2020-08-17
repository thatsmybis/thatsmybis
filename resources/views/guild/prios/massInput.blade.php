@extends('layouts.app')
@section('title', "Prios for " . $raid-> name . " " . $instance-> name . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">

            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium font-blizz">
                        <span class="fas fa-fw fa-dungeon text-muted"></span>
                        {{ $raid->name }} Prios for {{ $instance->name }}
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

            <form class="form-horizontal" role="form" method="POST" action="{{ route('guild.prios.massInput.submit', ['guildSlug' => $guild->slug, 'instanceSlug' => $instance->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="raid_id" value="{{ $raid->id }}">
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

                                <input hidden name="items[{{ $loop->iteration }}][id]" value="{{ $item->item_id }}">

                                <div class="col-lg-4 col-12">
                                    <div class="d-inline-grid align-middle text-5 mb-2">
                                        <label for="items[{{ $loop->iteration }}][name]" class="font-weight-bold d-none d-sm-block">
                                            <span class="sr-only">
                                                Item Name
                                            </span>
                                        </label>
                                        @include('partials/item', ['wowheadLink' => false, 'targetBlank' => true])
                                    </div>
                                </div>

                                <div class="col-lg-4 col-12">
                                    <label for="items[details]" class="font-weight-bold {{ $loop->iteration > 1 ? 'd-none' : '' }}">
                                        @if ($loop->first)
                                            <span class="fas fa-fw fa-question text-muted"></span>
                                            Details
                                        @else
                                            <span class="sr-only">Details</span>
                                        @endif
                                    </label>
                                    <ul class="fa-ul">
                                        @if ($item->guild_note)
                                            <li title="Note">
                                                <span class="fa-li"><span class="fas fa-fw fa- text-muted"></span></span>
                                                {{ $item->guild_note }}
                                            </li>
                                        @endif

                                        @if ($item->guild_priority)
                                            <li title="Priority note">
                                                <span class="fa-li"><span class="fas fa-fw fa-sort-amount-down text-muted"></span></span>
                                                {{ $item->guild_priority }}
                                            </li>
                                        @endif

                                        @if ($item->wishlistCharacters->count() > 0)
                                            <li title="Characters who have it wishlisted">
                                                <span class="fa-li"><span class="fas fa-fw fa-scroll-old text-legendary"></span></span>
                                                <ul class="list-inline">
                                                    @foreach ($item->wishlistCharacters as $character)
                                                        <li class="list-inline-item">
                                                            <a href="{{ route('character.show', ['guildSlug' => $guild->slug, 'name' => $character->name]) }}" class="text-{{ strtolower($character->class) }}" target="_blank">
                                                                {{ $character->name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif

                                        @if ($item->receivedAndRecipeCharacters->count() > 0)
                                            <li title="Characters who have received it">
                                                <span class="fa-li"><span class="fas fa-fw fa-sack text-success"></span></span>
                                                <ul class="list-inline">
                                                    @foreach ($item->receivedAndRecipeCharacters as $character)
                                                        <li class="list-inline-item">
                                                            <a href="{{ route('character.show', ['guildSlug' => $guild->slug, 'name' => $character->name]) }}" class="text-{{ strtolower($character->class) }}" target="_blank">
                                                                {{ $character->name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                <div class="col-lg-4 col-12 {{ $errors->has('items.' . $loop->iteration . '.*') || $errors->has('items.' . $loop->iteration . '.id') ? 'bg-danger rounded font-weight-bold' : '' }}">
                                    <div class="form-group mb-2 col-md-8 col-sm-10 col-12">
                                        <label for="items[{{ $loop->iteration }}][character_ids]" class="font-weight-bold {{ $loop->iteration > 1 ? 'd-none' : '' }}">
                                            @if ($loop->first)
                                                <span class="fas fa-fw fa-sort-amount-down text-muted"></span>
                                                Prio'd Characters
                                            @else
                                                <span class="sr-only">Priority Characters</span>
                                            @endif
                                        </label>

                                        <select name="" class="js-input-select form-control dark selectpicker" data-live-search="true" autocomplete="off">
                                            <option value="">
                                                —
                                            </option>

                                            @foreach ($guild->characters as $character)
                                                <option value="{{ $character->id }}"
                                                    data-tokens="{{ $character->id }}"
                                                    data-raid-id="{{ $character->raid_id }}"
                                                    class="js-character-option text-{{ strtolower($character->class) }}-important"
                                                    {{ old('items.' . $loop->iteration . '.character_id') && old('items.' . $loop->iteration . '.character_id') == $character->id  ? 'selected' : '' }}>
                                                    {{ $character->name }} &nbsp; {{ $character->class ? '(' . $character->class . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <ul class="js-sortable-lazy no-bullet no-indent mt-3 mb-0">
                                            @for ($i = 0; $i < $maxPrios; $i++)
                                                <li class="input-item {{ $errors->has('items.' . $loop->iteration . '.characters.' . $i ) ? 'text-danger font-weight-bold' : '' }}"
                                                    style="{{ old('items.' . $loop->iteration . '.characters.' . $i) || $item->priodCharacters->get($i) ? '' : 'display:none;' }}">

                                                    <input type="checkbox" checked name="items[{{ $loop->iteration }}][characters][{{ $i }}][id]"
                                                        value="{{ old('items.' . $loop->iteration . '.characters.' . $i . '.id') ? old('items.' . $loop->iteration . '.characters.' . $i . '.id') : ($item->priodCharacters->get($i) ? $item->priodCharacters->get($i)->id : '') }}" style="display:none;">
                                                    <input type="checkbox" checked name="items[{{ $loop->iteration }}][characters][{{ $i }}][label]"
                                                        value="{{ old('items.' . $loop->iteration . '.characters.' . $i . '.label') ? old('items.' . $loop->iteration . '.characters.' . $i . '.label') : ($item->priodCharacters->get($i) ? $item->priodCharacters->get($i)->name : '') }}" style="display:none;">
                                                    <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                                    <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('items.' . $loop->iteration . '.characters.' . $i . '.label') ? old('items.' . $loop->iteration . '.characters.' . $i . '.label') : ($item->priodCharacters->get($i) ? $item->priodCharacters->get($i)->name : '') }}</span>&nbsp;

                                                </li>
                                                @if ($errors->has('items.' . $loop->iteration . '.characters.*'))
                                                    <li class="'text-danger font-weight-bold'">
                                                        {{ $errors->first('items.' . $loop->iteration . '.characters.*') }}
                                                    </li>
                                                @endif
                                            @endfor
                                        </ul>

                                        @if ($errors->has('items.' . $loop->iteration . '.*'))
                                            <div class="'text-danger font-weight-bold'">
                                                {{ $errors->first('items.' . $loop->iteration . '.*') }}
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
<script>
    /**
     * Move the selected value to the list under the select.
     * Change the selected value back to the default value.
     **/
     that = null;
    $(".js-input-select").change(function () {
        $(this).find(":selected").val();
        $(this).find(":selected").html().trim();

        value = $(this).find(":selected").val();
        label = $(this).find(":selected").html().trim();
        $nextInput = $(this).parent().next("ul").children("li").children("input[value='']").first();

        if ($nextInput.val() == "") {
        // Add the item.
            $nextInput.parent("li").show();
            // Populate the ID
            $nextInput.val(value);
            $nextInput.siblings(".js-input-label").html(" " + label);
            // Populate the label
            $label = $nextInput.next("input").first();
            $label.val(label);

            // Reset the select
            $(this).val("");
            $(this).find("option:first").text("—");
        } else {
        // Can't add any more.
            $(this).val("");
            // If a select input triggered this
            $(this).find("option:first").text("maximum added");
        }
    });
</script>
@endsection

@section('wowheadIconSize', 'medium')
