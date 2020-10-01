@extends('layouts.app')
@section('title', "Assign Loot - " . config('app.name'))

@php
    $maxDate = (new \DateTime())->modify('+1 day')->format('Y-m-d');
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium">
                        <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                        Assign Loot
                    </h1>
                    <small>
                        <strong>Hint:</strong> Keep the roster and/or item pages open in another window to review who deserves what
                        <br>
                        <strong>Note:</strong> If a character has the same item prio'd in multiple raids, we'll only remove/flag the first one we find.
                    </small>
                </div>
            </div>

            <div class="row mt-4 mb-4">
                <div class="col-12 pt-2 pb-2 bg-light rounded">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="toggle_notes" value="1" class="" autocomplete="off"
                                            {{ old('toggle_notes') && old('toggle_notes') != 1 ? '' : 'checked' }}>
                                            Show note inputs
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="toggle_dates" value="1" class="" autocomplete="off"
                                            {{ old('toggle_dates') && old('toggle_dates') == 1 ? 'checked' : '' }}>
                                            Show date inputs <span class="text-muted small">for backdating old loot</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="default_datepicker" class="row" style="display:none;">
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="date_default" class="font-weight-bold">
                                    <span class="fas fa-fw fa-calendar-alt text-muted"></span>
                                    Set default date <span class="text-muted small">optional, overwrites all dates</span>
                                </label>
                                <input name="date_default" min="2019-08-26" max="{{ $maxDate }}" type="date" placeholder="defaults to today" class="form-control dark" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form class="form-horizontal" role="form" method="POST" action="{{ route('item.massInput.submit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}
                <div class="row mt-4 mb-4">
                    <div class="col-lg-3 col-sm-6 col-12 pt-2 mb-2">
                        <label for="raid_id font-weight-light">
                            <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                            Raid
                        </label>
                        <select id="raid_id" class="form-control dark">
                            <option value="">—</option>
                            @foreach ($guild->raids as $raid)
                                <option value="{{ $raid->id }}" style="color:{{ $raid->getColor() }};">
                                    {{ $raid->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-12 pt-2 mb-2">
                        <label for="raid_filter font-weight-light">
                            <span class="text-muted">Character filter</span>
                        </label>
                        <select id="raid_filter" class="form-control dark">
                            <option value="">—</option>
                            @foreach ($guild->raids as $raid)
                                <option value="{{ $raid->id }}" style="color:{{ $raid->getColor() }};">
                                    {{ $raid->name }}
                                </option>
                            @endforeach
                        </select>
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

                <div class="row">
                    <div class="col-12 mt-3 mb-3 bg-light rounded">
                        @for ($i = 0; $i < 125; $i++)
                            <div class="row striped-light pb-4 pt-4 rounded {{ $i > 2 ? 'js-hide-empty' : '' }}" style="{{ $i > 2 ? 'display:none;' : '' }}">

                                <!-- Item input -->
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group mb-0 {{ $errors->has('items.' . $i . '.id') ? 'text-danger font-weight-bold' : '' }}">

                                        <label for="name" class="font-weight-bold">
                                            <span class="fas fa-fw fa-sack text-success"></span>
                                            @if ($i == 0)
                                                Item
                                            @else
                                                <span class="sr-only">
                                                    Item
                                                </span>
                                            @endif
                                        </label>

                                        <input data-max-length="50" data-is-single-input="1" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text js-show-next form-control dark" autocomplete="off">
                                        <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                        <ul class="no-bullet no-indent mb-0">
                                            <li class="input-item {{ $errors->has('items.' . $i . '.id') ? 'text-danger font-weight-bold' : '' }}" style="{{ old('items.' . $i . '.id') ? '' : 'display:none;' }}">
                                                <input type="checkbox" checked name="items[{{ $i }}][id]" value="{{ old('items.' . $i . '.id') ? old('items.' . $i . '.id') : '' }}" autocomplete="off" style="display:none;">
                                                <input type="checkbox" checked name="items[{{ $i }}][label]" value="{{ old('items.' . $i . '.label') ? old('items.' . $i . '.label') : '' }}" autocomplete="off" style="display:none;">
                                                <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                                <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('items.' . $i . '.label') ? old('items.' . $i . '.label') : '' }}</span>&nbsp;
                                            </li>
                                            @if ($errors->has('items.' . $i . '.id'))
                                                <li class="'text-danger font-weight-bold'">
                                                    {{ $errors->first('items.' . $i . '.id') }}
                                                </li>
                                            @endif
                                            @if ($i == 124)
                                                <li class="text-danger font-weight-bold">
                                                    Max items added
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                <!-- Character dropdown -->
                                <div class="col-lg-2 col-sm-4 col-10">
                                    <div class="form-group mb-0 {{ $errors->has('items.' . $i . '.character_id') ? 'text-danger font-weight-bold' : '' }}">

                                        <label for="member_id" class="font-weight-bold">
                                            @if ($i == 0)
                                                <span class="fas fa-fw fa-user text-muted"></span>
                                                Character
                                            @else
                                                &nbsp;
                                                <span class="sr-only">
                                                    Character
                                                </span>
                                            @endif
                                        </label>

                                        <select name="items[{{ $i }}][character_id]" class="js-show-next form-control dark selectpicker" data-live-search="true" autocomplete="off">
                                            <option value="">
                                                —
                                            </option>

                                            @foreach ($guild->characters as $character)
                                                <option value="{{ $character->id }}"
                                                    data-tokens="{{ $character->id }}"
                                                    data-raid-id="{{ $character->raid_id }}"
                                                    class="js-character-option text-{{ strtolower($character->class) }}-important"
                                                    {{ old('items.' . $i . '.character_id') && old('items.' . $i . '.character_id') == $character->id  ? 'selected' : '' }}>
                                                    {{ $character->name }} &nbsp; {{ $character->class ? '(' . $character->class . ')' : '' }} &nbsp; {{ $character->is_alt ? "Alt" : '' }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('items.' . $i))
                                            <div class="'text-danger font-weight-bold'">
                                                {{ $errors->first('items.' . $i) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-lg-1 col-sm-2 col-2">
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            @if ($i == 0)
                                                Offspec
                                            @else
                                                <span class="sr-only">
                                                    Offspec
                                                </span>
                                                &nbsp;
                                            @endif
                                        </label>
                                        <div class="checkbox">
                                            <label title="item is offspec">
                                                <input type="checkbox" name="items[{{ $i }}][is_offspec]" value="1" class="" autocomplete="off"
                                                    {{ old('items.' . $i . '.is_offspec') && old('items.' . $i . '.is_offspec') == 1  ? 'checked' : '' }}>
                                                    OS
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Note -->
                                <div class="js-note col-lg-3 col-sm-6 col-12">
                                    <div class="form-group mb-0 {{ $errors->has('items.' . $i . '.note') ? 'text-danger font-weight-bold' : '' }}">

                                        <label for="items[{{ $i }}][note]" class="font-weight-bold">
                                            @if ($i == 0)
                                                <span class="fas fa-fw fa-scroll text-muted"></span>
                                                Note
                                                <span class="text-muted small">optional</span>
                                            @else
                                                &nbsp;
                                                <span class="sr-only">
                                                    Optional Note
                                                </span>
                                            @endif
                                        </label>
                                        <input name="items[{{ $i }}][note]" data-max-length="140" type="text" placeholder="brief public note" class="js-show-next form-control dark" autocomplete="off"
                                            {{ old('items.' . $i . '.note') ? old('items.' . $i . '.note') : '' }}>
                                    </div>
                                </div>

                                <!-- Officer Note -->
                                <div class="js-note col-lg-3 col-sm-6 col-12">
                                    <div class="form-group mb-0 {{ $errors->has('items.' . $i . '.officer_note') ? 'text-danger font-weight-bold' : '' }}">

                                        <label for="items[{{ $i }}][officer_note]" class="font-weight-bold">
                                            @if ($i == 0)
                                                <span class="fas fa-fw fa-scroll text-muted"></span>
                                                Officer Note
                                                <span class="text-muted small">optional</span>
                                            @else
                                                &nbsp;
                                                <span class="sr-only">
                                                    Optional Officer Note
                                                </span>
                                            @endif
                                        </label>
                                        <input name="items[{{ $i }}][officer_note]" data-max-length="140" type="text" placeholder="officer note" class="js-show-next form-control dark" autocomplete="off"
                                            {{ old('items.' . $i . '.officer_note') ? old('items.' . $i . '.officer_note') : '' }}>
                                    </div>
                                </div>

                                <!-- Date -->
                                <div class="js-date col-lg-3 col-sm-6 col-12" style="{{ old('items.' . $i . '.received_at') ? '' : 'display:none;' }}">
                                    <div class="form-group mb-0 {{ $errors->has('items.' . $i . '.received_at') ? 'text-danger font-weight-bold' : '' }}">

                                        <label for="items[{{ $i }}][received_at]" class="font-weight-bold">
                                            @if ($i == 0)
                                                <span class="fas fa-fw fa-calendar-alt text-muted"></span>
                                                Date
                                                <span class="text-muted small">optional</span>
                                            @else
                                                &nbsp;
                                                <span class="sr-only">
                                                    Optional Date
                                                </span>
                                            @endif
                                        </label>
                                        <input name="items[{{ $i }}][received_at]" min="2019-08-26" max="{{ $maxDate }}" type="date" placeholder="defaults to today" class="js-show-next form-control dark" autocomplete="off"
                                            {{ old('items.' . $i . '.received_at') ? old('items.' . $i . '.received_at') : '' }}>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="form-group mb-0">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="delete_wishlist_items" value="1" class="" autocomplete="off"
                                        {{ (old('delete_wishlist_items') && old('delete_wishlist_items') == 1) || (!old('delete_wishlist_items') && $guild->is_wishlist_autopurged) ? 'checked' : '' }}>
                                        Delete assigned items from each character's wishlist <abbr title="if unchecked, corresponding wishlist items will be flagged as received but still be visible">?</abbr>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="delete_prio_items" value="1" class="" autocomplete="off"
                                        {{ (old('delete_prio_items') && old('delete_prio_items') == 1) || (!old('delete_prio_items') && $guild->is_prio_autopurged) ? 'checked' : '' }}>
                                        Delete assigned items from each character's prio list <abbr title="if unchecked, corresponding prio will be flagged as received but still be visible">?</abbr>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-success" onclick="return confirm('All done?');"><span class="fas fa-fw fa-save"></span> Submit</button>
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
    var guild = {!! $guild->toJson() !!};
</script>
<script src="{{ env('APP_ENV') == 'local' ? asset('/js/itemMassInput.js') : mix('js/processed/itemMassInput.js') }}"></script>
@endsection
