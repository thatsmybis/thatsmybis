@extends('layouts.app')
@section('title', "Raid Time! - " . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12">
                    <h1>
                        <span class="fas fa-fw fa-axe-battle text-gold"></span>
                        Raid Time!
                    </h1>
                    <small>
                        <strong>Hint:</strong> Keep the roster and/or item page(s) open in another window to review who deserves what!
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

            <form class="form-horizontal" role="form" method="POST" action="{{ route('item.massInput.submit', ['guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                @for ($i = 0; $i < 125; $i++)
                    <div class="row striped-light mt-3 mb-3 pt-4 pb-4 rounded {{ $i > 2 ? 'js-hide-empty' : '' }}" style="{{ $i > 2 ? 'display:none;' : '' }}">

                        <div class="col-sm-6 col-12">
                            <div class="form-group mb-0 {{ $errors->has('items.*') ? 'has-error' : '' }}">
                                @if ($i == 0)
                                    <label for="name" class="font-weight-bold">
                                        <span class="fas fa-fw fa-sack text-success"></span>
                                        Item
                                    </label>
                                @endif

                                <input data-max-length="50" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text js-show-next form-control" autocomplete="off">
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
                                @if ($i == 0)
                                    <label for="member_id" class="font-weight-bold d-none d-sm-block">
                                        <span class="fas fa-fw fa-user text-muted"></span>
                                        Character
                                    </label>
                                @endif
                                <select name="items[{{ $i }}][character_id]" class="js-show-next form-control selectpicker" data-live-search="true" autocomplete="off">
                                    <option value="" class="bg-tag">
                                        —
                                    </option>

                                    @foreach ($guild->characters as $character)
                                        <option value="{{ $character->id }}"
                                            data-tokens="{{ $character->id }}"
                                            class="bg-tag text-{{ strtolower($character->class) }}-important"
                                            {{ old('items.' . $loop->iteration . '.character_id') && old('items.' . $loop->iteration . '.character_id') == $character->id  ? 'selected' : '' }}>
                                            {{ $character->name }} &nbsp; ({{ $character->class }})
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

                <div class="form-group mt-4">
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
    $(".js-show-next").change(function() {
        showNext(this);
    });
    $(".js-show-next").keyup(function() {
        showNext(this);
    });

    $(".js-show-next").change(function() {
        showNext(this);
    }).change();

    $(".js-show-next").keyup(function() {
        showNext(this);
    });

    // If the current element has a value, show it and the next element that is hidden because it is empty
    function showNext(currentElement) {
        if ($(currentElement).val() != "") {
            $(currentElement).show();
            $(currentElement).closest(".row").next(".js-hide-empty").show();
        }
    }
</script>
@endsection
