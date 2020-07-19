@extends('layouts.app')
@section('title', "Raid Input - " . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12">
                    <h1>Raid Input</h1>
                </div>
            </div>

            <hr class="light">

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

                @for ($i = 0; $i < 120; $i++)
                    <div class="row mb-4 {{ $i > 2 ? 'js-hide-empty' : '' }}" style="{{ $i > 2 ? 'display:none;' : '' }}">

                        <div class="col-sm-6 col-12">
                            <div class="form-group {{ $errors->has('items.*') ? 'has-error' : '' }}">
                                @if ($i == 0)
                                    <label for="name" class="font-weight-bold">
                                        Item
                                    </label>
                                @endif

                                <input data-max-length="50" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text js-show-next form-control">
                                <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                <ul class="no-bullet no-indent mb-0">
                                    <li class="input-item" style="{{ old('items.' . $i) ? '' : 'display:none;' }}">
                                        <input type="checkbox" checked name="items[]" value="{{ old('items.' . $i) ? old('items.' . $i) : '' }}" style="display:none;">
                                        <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                        <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('items.' . $i) ? old('items.' . $i) : '' }}</span>&nbsp;
                                    </li>
                                </ul>
                                <div>
                                    @if ($errors->has('wishlist.*'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('wishlist.*') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-12">
                            <div class="form-group {{ $errors->has('characters.*') ? 'has-error' : '' }}">
                                @if ($i == 0)
                                    <label for="member_id" class="font-weight-bold">
                                        Raider
                                    </label>
                                @endif
                                <div class="form-group">
                                    <select name="characters[]" class="js-show-next form-control selectpicker" data-live-search="true">
                                        <option value="" class="bg-tag">
                                            —
                                        </option>

                                        @foreach ($guild->characters as $character)
                                            <option value="{{ $character->id }}"
                                                data-tokens="{{ $character->id }}"
                                                class="bg-tag text-{{ strtolower($character->class) }}-important"
                                                {{ old('characters.' . $loop->iteration) && old('characters.' . $loop->iteration) == $character->id  ? 'selected' : '' }}>
                                                {{ $character->name }} &nbsp; ({{ $character->class }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                @endfor

                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Submit</button>
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
