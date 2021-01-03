@extends('layouts.app')
@section('title', "Prios for " . $raid-> name . " " . $item-> name . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-12">

            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium font-blizz">
                        <span class="fas fa-fw fa-sack text-muted"></span>
                        {{ $raid->name }} Prios
                    </h1>
                    <small>
                        <strong>Note:</strong> When someone receives an item, we'll attempt to automatically remove it from their prios. If they have the same item prio'd in multiple raids, we'll remove only the first one we find.
                    </small>
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

            <form class="form-horizontal" role="form" method="POST" action="{{ route('guild.item.prios.submit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="raid_id" value="{{ $raid->id }}">
                <input hidden name="item_id" value="{{ $item->item_id }}">
                <input hidden name="items[{{ $item->item_id }}][item_id]" value="{{ $item->item_id }}">

                <div class="row mt-3 mb-3 bg-light rounded">
                    <div class="col-lg-4 col-12">
                        <div class="d-inline-grid align-middle text-5 mb-2">
                            <label for="items[{{ $item->item_id }}][name]" class="font-weight-bold d-none d-sm-block">
                                <span class="sr-only">
                                    Item Name
                                </span>
                            </label>
                            @include('partials/item', ['wowheadLink' => false, 'targetBlank' => true])
                        </div>
                    </div>

                    <div class="col-lg-5 col-12">
                        <label for="items[details]" class="font-weight-bold">
                            <span class="fas fa-fw fa- text-muted"></span>
                            Notes
                        </label>
                        <ul class="fa-ul">
                            @if ($item->guild_note)
                                <li title="Note">
                                    <span class="fa-li"><span class="fal fa-fw fa-sticky-note text-muted"></span></span>
                                    {{ $item->guild_note }}
                                </li>
                            @endif

                            @if ($item->guild_priority)
                                <li title="Priority note">
                                    <span class="fa-li"><span class="fal fa-fw fa-sort-amount-down text-muted"></span></span>
                                    {{ $item->guild_priority }}
                                </li>
                            @endif

                            @if ($item->wishlistCharacters->count() > 0)
                                <li title="Characters who have it wishlisted">
                                    <span class="fa-li"><span class="fal fa-fw fa-scroll-old text-legendary"></span></span>
                                    <ul class="list-inline">
                                        @foreach ($item->wishlistCharacters as $character)
                                             <li class="list-inline-item">
                                                <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}"
                                                    class="tag {{ $character->pivot->is_received ? 'font-strikethrough' : '' }}" target="_blank">
                                                    <span class="text-muted">{{ $character->pivot->order ? $character->pivot->order : '' }}</span>
                                                    <!--<span class="role-circle" style="background-color:{{ getHexColorFromDec($character->raid_color) }}"></span>-->
                                                    <span class="text-{{ strtolower($character->class) }}">{{ $character->name }}</span>
                                                    <span class="js-watchable-timestamp smaller text-muted"
                                                        data-timestamp="{{ $character->pivot->created_at }}"
                                                        data-is-short="1">
                                                    </span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif

                            @if ($item->receivedAndRecipeCharacters->count() > 0)
                                <li title="Characters who have received it">
                                    <span class="fa-li"><span class="fal fa-fw fa-sack text-success"></span></span>
                                    <ul class="list-inline">
                                        @foreach ($item->receivedAndRecipeCharacters as $character)
                                            <li class="list-inline-item">
                                                <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}"
                                                    class="tag text-{{ strtolower($character->class) }}" target="_blank">
                                                    {{ $character->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="col-lg-3 col-12 {{ $errors->has('item.*') ? 'bg-danger rounded font-weight-bold' : '' }}">
                        <div class="form-group mb-2">
                            <label for="items[{{ $item->item_id }}][characters]" class="font-weight-bold">
                                <span class="fas fa-fw fa-sort-amount-down text-muted"></span>
                                Prio'd Characters
                                <span class="text-muted font-weight-normal small">max {{ $maxPrios }}</span>
                            </label>

                            <select name="" class="js-input-select form-control dark selectpicker" data-live-search="true" autocomplete="off">
                                <option value="">
                                    —
                                </option>

                                @foreach ($guild->characters as $character)
                                    <option value="{{ $character->id }}"
                                        data-tokens="{{ $character->id }}"
                                        data-raid-id="{{ $character->raid_id }}"
                                        class="js-character-option text-{{ strtolower($character->class) }}-important">
                                        {{ $character->name }} &nbsp; {{ $character->class ? '(' . $character->class . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            <ol class="js-sortable-lazy no-indent mt-3 mb-0">
                                @for ($i = 0; $i < $maxPrios; $i++)
                                    <li class="input-item {{ $errors->has('items.' . $item->item_id . '.characters.' . $i ) ? 'text-danger font-weight-bold' : '' }}"
                                        style="{{ old('items.' . $item->item_id . '.characters.' . $i) && old('items.' . $item->item_id . '.characters.' . $i)['character_id'] || $item->priodCharacters->get($i) ? '' : 'display:none;' }}">

                                        <input type="checkbox" checked name="items[{{ $item->item_id }}][characters][{{ $i }}][character_id]"
                                            value="{{ old('items.' . $item->item_id . '.characters.' . $i . '.character_id') ? old('item.' . $item->item_id . '.characters.' . $i . '.character_id') : ($item->priodCharacters->get($i) ? $item->priodCharacters->get($i)->id : '') }}" style="display:none;">
                                        <input type="checkbox" checked name="items[{{ $item->item_id }}][characters][{{ $i }}][label]"
                                            value="{{ old('items.' . $item->item_id . '.characters.' . $i . '.label') ? old('items.' . $item->item_id . '.characters.' . $i . '.label') : ($item->priodCharacters->get($i) ? $item->priodCharacters->get($i)->name : '') }}" style="display:none;">
                                        <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                        <span class="js-sort-handle js-input-label move-cursor text-unselectable">{!! old('items.' . $item->item_id . '.characters.' . $i . '.label') ? old('items.' . $item->item_id . '.characters.' . $i . '.label') : ($item->priodCharacters->get($i) ? $item->priodCharacters->get($i)->name . ' (' . $item->priodCharacters->get($i)->class . ')' : '') !!}</span>&nbsp;

                                    </li>
                                    @if ($errors->has('items.' . $item->item_id . '.characters.*'))
                                        <li class="'text-danger font-weight-bold'">
                                            {{ $errors->first('items.' . $item->item_id . '.characters.*') }}
                                        </li>
                                    @endif
                                @endfor
                            </ol>

                            @if ($errors->has('item.*'))
                                <div class="'text-danger font-weight-bold'">
                                    {{ $errors->first('item.*') }}
                                </div>
                            @endif
                        </div>
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
<script src="{{ env('APP_ENV') == 'local' ? asset('/js/autocomplete.js') : mix('js/processed/autocomplete.js') }}"></script>
@endsection

@section('wowheadIconSize', 'medium')
