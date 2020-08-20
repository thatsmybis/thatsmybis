@extends('layouts.app')
@section('title', "Assign Loot - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">

            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium">
                        <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                        Assign Loot
                    </h1>
                    <small>
                        <strong>Hint:</strong> Keep the roster and/or item pages open in another window to review who deserves what
                        <br>
                        <strong>Note:</strong> When someone receives an item, we'll attempt to automatically remove it from their wishlist/prios. If they have the same item prio'd in multiple raids, we'll remove only the first one we find.
                    </small>
                </div>

                <div class="col-sm-6 col-12 pt-2 mb-2">
                    <label for="raid_filter font-weight-light">
                        <span class="text-muted fas fa-fw fa-helmet-battle"></span>
                        Raid
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

            <form class="form-horizontal" role="form" method="POST" action="{{ route('item.massInput.submit', ['guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-12 mt-3 mb-3 bg-light rounded">
                        @for ($i = 0; $i < 125; $i++)
                            <div class="row striped-light pb-4 pt-4 rounded {{ $i > 2 ? 'js-hide-empty' : '' }}" style="{{ $i > 2 ? 'display:none;' : '' }}">

                                <div class="col-sm-6 col-12">
                                    <div class="form-group mb-0 {{ $errors->has('items.' . $i . '.id') ? 'text-danger font-weight-bold' : '' }}">

                                        <label for="name" class="font-weight-bold">
                                            <span class="fas fa-fw fa-sack text-success"></span>
                                            @if ($i == 0)
                                                Item
                                            @endif
                                        </label>


                                        <input data-max-length="50" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text js-show-next form-control dark" autocomplete="off">
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

                                <div class="col-sm-6 col-12">
                                    <div class="form-group mb-0 {{ $errors->has('items.' . $i . '.character_id') ? 'text-danger font-weight-bold' : '' }}">

                                        <label for="member_id" class="font-weight-bold d-none d-sm-block">
                                            &nbsp;
                                            @if ($i == 0)
                                                <span class="fas fa-fw fa-user text-muted"></span>
                                                Character
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
                                                    {{ old('items.' . $loop->iteration . '.character_id') && old('items.' . $loop->iteration . '.character_id') == $character->id  ? 'selected' : '' }}>
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
                            </div>
                        @endfor
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
<script src="{{ env('APP_ENV') == 'local' ? asset('/js/itemMassInput.js') : mix('js/processed/itemMassInput.js') }}"></script>
@endsection
