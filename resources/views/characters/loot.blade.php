@extends('layouts.app')
@section('title', "Loot for " . $character->name . " - " . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12">
                    @include('characters/partials/header', ['showEdit' => false, 'titleSuffix' => "'s loot"])
                </div>
            </div>

            <hr class="light">

            <div class="row">
                <div class="col-12">
                    <span class="font-weight-bold">Hint:</span> Your top 4 items are displayed first; make them count.
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

            <form class="form-horizontal" role="form" method="POST" action="{{ route('character.updateLoot', ['guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $character->id }}" />

                <div class="form-group mt-4 mb-4">
                    <div class="row">
                        <div class="col-md-8 col-sm-10 col-12">
                            <label for="wishlist">
                                <span class="font-weight-bold text-legendary">
                                    <span class="fas fa-fw fa-scroll-old"></span>
                                    Wishlist
                                </span>
                                <small class="text-muted font-weight-normal">max {{ $maxWishlistItems }}</small>
                            </label>

                            <div class="{{ $errors->has('wishlist.*') ? 'has-error' : '' }}">
                                <input id="wishlist" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text form-control">
                                <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                <ul class="js-sortable no-bullet no-indent mb-0">
                                    @for ($i = 0; $i < $maxWishlistItems; $i++)
                                        <li class="input-item" style="{{ old('wishlist.' . $i) || ($character->wishlist && $character->wishlist->get($i)) ? '' : 'display:none;' }}">
                                            <input type="checkbox" checked name="wishlist[]" value="{{ old('wishlist.' . $i) ? old('wishlist.' . $i) : ($character->wishlist && $character->wishlist->get($i) ? $character->wishlist->get($i)->item_id : '') }}" style="display:none;">
                                            <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                            <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('wishlist.' . $i) ? old('wishlist.' . $i) : ($character->wishlist && $character->wishlist->get($i) ? $character->wishlist->get($i)->name : '') }}</span>&nbsp;
                                        </li>
                                    @endfor
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
                    </div>
                </div>

                <hr class="light">

                <div class="form-group mt-4 mb-4">
                    <div class="row">
                        <div class="col-md-8 col-sm-10 col-12">
                            <label for="received">
                                <span class="font-weight-bold text-success">
                                    <span class="fas fa-fw fa-sack"></span>
                                    Loot Received
                                </span>
                                <small class="text-muted font-weight-normal">Max {{ $maxReceivedItems }}</small>
                            </label>

                            <div class="{{ $errors->has('received.*') ? 'has-error' : '' }}">
                                <input id="received" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text form-control">
                                <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                <ul class="js-sortable no-bullet no-indent mb-0">
                                    @for ($i = 0; $i < $maxReceivedItems; $i++)
                                        <li class="input-item" style="{{ old('received.' . $i) || ($character->received && $character->received->get($i)) ? '' : 'display:none;' }}">
                                            <input type="checkbox" checked name="received[]" value="{{ old('received.' . $i) ? old('received.' . $i) : ($character->received && $character->received->get($i) ? $character->received->get($i)->item_id : '') }}" style="display:none;">
                                            <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                            <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('received.' . $i) ? old('received.' . $i) : ($character->received && $character->received->get($i) ? $character->received->get($i)->name : '') }}</span>&nbsp;
                                        </li>
                                    @endfor
                                </ul>
                                <div>
                                    @if ($errors->has('received.*'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('received.*') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="light">

                <div class="form-group mt-4 mb-4">
                    <div class="row">
                        <div class="col-md-8 col-sm-10 col-12">
                            <label for="recipes">
                                <span class="font-weight-bold text-rare text-rare">
                                    <span class="fas fa-fw fa-book"></span>
                                    Rare Recipes
                                </span>
                                <small class="text-muted font-weight-normal">Max {{ $maxRecipes }}</small>
                            </label>

                            <div class="{{ $errors->has('recipes.*') ? 'has-error' : '' }}">
                                <input id="recipes" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text form-control">
                                <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                <ul class="js-sortable no-bullet no-indent mb-0">
                                    @for ($i = 0; $i < $maxRecipes; $i++)
                                        <li class="input-item" style="{{ old('recipes.' . $i) || ($character->recipes && $character->recipes->get($i)) ? '' : 'display:none;' }}">
                                            <input type="checkbox" checked name="recipes[]" value="{{ old('recipes.' . $i) ? old('recipes.' . $i) : ($character->recipes && $character->recipes->get($i) ? $character->recipes->get($i)->item_id : '') }}" style="display:none;">
                                            <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                            <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('recipes.' . $i) ? old('recipes.' . $i) : ($character->recipes && $character->recipes->get($i) ? $character->recipes->get($i)->name : '') }}</span>&nbsp;
                                        </li>
                                    @endfor
                                </ul>
                                <div>
                                    @if ($errors->has('recipes.*'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('recipes.*') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <hr class="light">
                    </div>
                </div>

                <div class="row mt-4 mb-4">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="public_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                Public Note
                                <small class="text-muted">anyone in the guild can see this</small>
                            </label>
                            <textarea data-max-length="2000" name="public_note" rows="2" placeholder="anyone in the guild can see this" class="form-control">{{ old('public_note') ? old('public_note') : ($character ? $character->public_note : '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- TODO: Permissions for who can see/set this -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="officer_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-shield"></span>
                                Officer Note
                                <small class="text-muted">only officers can see this</small>
                            </label>
                            <textarea data-max-length="2000" name="officer_note" rows="2" placeholder="only officers can see this" class="form-control">{{ old('officer_note') ? old('officer_note') : ($character ? $character->officer_note : '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- TODO: Permissions for who can see/set this -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="personal_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-lock"></span>
                                Personal Note
                                <small class="text-muted">only you can see this</small>
                            </label>
                            <textarea data-max-length="2000" name="personal_note" rows="2" placeholder="only you can see this" class="form-control">{{ old('personal_note') ? old('personal_note') : ($character ? $character->personal_note : '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-5">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
