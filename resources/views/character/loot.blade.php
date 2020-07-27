@extends('layouts.app')
@section('title', "Loot for " . $character->name . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-3">
                <div class="col-12 pt-2 bg-lightest rounded">
                    @include('character/partials/header', ['headerSize' => 1, 'showEdit' => false, 'titleSuffix' => "'s loot"])
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <small>
                        <span class="font-weight-bold">Hint:</span> Your top 4 items are displayed first; make them count
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

            <form class="form-horizontal" role="form" method="POST" action="{{ route('character.updateLoot', ['guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $character->id }}" />

                <div class="row mb-3 pt-2 bg-light rounded">
                    <div class="form-group mb-2 col-md-8 col-sm-10 col-12">
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
                                    <li class="input-item {{ $errors->has('wishlist.' . $i . '.item_id') ? 'text-danger font-weight-bold' : '' }}" style="{{ old('wishlist.' . $i . '.item_id') || ($character->wishlist && $character->wishlist->get($i)) ? '' : 'display:none;' }}">
                                        <input type="checkbox" checked name="wishlist[{{ $i }}][item_id]" value="{{ old('wishlist.' . $i . '.item_id') ? old('wishlist.' . $i . '.item_id') : ($character->wishlist && $character->wishlist->get($i) ? $character->wishlist->get($i)->item_id : '') }}" style="display:none;">
                                        <input type="checkbox" checked name="wishlist[{{ $i }}][label]" value="{{ old('wishlist.' . $i . '.label') ? old('wishlist.' . $i . '.label') : ($character->wishlist && $character->wishlist->get($i) ? $character->wishlist->get($i)->name : '') }}" style="display:none;">
                                        <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                        <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('wishlist.' . $i . '.label') ? old('wishlist.' . $i . '.label') : ($character->wishlist && $character->wishlist->get($i) ? $character->wishlist->get($i)->name : '') }}</span>&nbsp;
                                    </li>
                                    @if ($errors->has('wishlist.' . $i . '.item_id'))
                                        <li class="'text-danger font-weight-bold'">
                                            {{ $errors->first('wishlist.' . $i . '.item_id') }}
                                        </li>
                                    @endif
                                @endfor
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 pt-2 bg-light rounded">
                    <div class="form-group mb-2 col-md-8 col-sm-10 col-12">
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
                                    <li class="input-item {{ $errors->has('received.' . $i . '.item_id') ? 'text-danger font-weight-bold' : '' }}" style="{{ old('received.' . $i . '.item_id') || ($character->received && $character->received->get($i)) ? '' : 'display:none;' }}">
                                        <input type="checkbox" checked name="received[{{ $i }}][item_id]" value="{{ old('received.' . $i . '.item_id') ? old('received.' . $i . '.item_id') : ($character->received && $character->received->get($i) ? $character->received->get($i)->item_id : '') }}" style="display:none;">
                                        <input type="checkbox" checked name="received[{{ $i }}][label]" value="{{ old('received.' . $i . '.label') ? old('received.' . $i . '.label') : ($character->received && $character->received->get($i) ? $character->received->get($i)->name : '') }}" style="display:none;">
                                        <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                        <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('received.' . $i . '.label') ? old('received.' . $i . '.label') : ($character->received && $character->received->get($i) ? $character->received->get($i)->name : '') }}</span>&nbsp;
                                    </li>
                                    @if ($errors->has('received.' . $i . '.item_id'))
                                        <li class="'text-danger font-weight-bold'">
                                            {{ $errors->first('received.' . $i . '.item_id') }}
                                        </li>
                                    @endif
                                @endfor
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 pt-2 bg-light rounded">
                    <div class="form-group mb-2 col-md-8 col-sm-10 col-12">
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
                                    <li class="input-item {{ $errors->has('recipes.' . $i . '.item_id') ? 'text-danger font-weight-bold' : '' }}" style="{{ old('recipes.' . $i . '.item_id') || ($character->recipes && $character->recipes->get($i)) ? '' : 'display:none;' }}">
                                        <input type="checkbox" checked name="recipes[{{ $i }}][item_id]" value="{{ old('recipes.' . $i . '.item_id') ? old('recipes.' . $i . '.item_id') : ($character->recipes && $character->recipes->get($i) ? $character->recipes->get($i)->item_id : '') }}" style="display:none;">
                                        <input type="checkbox" checked name="recipes[{{ $i }}][label]" value="{{ old('recipes.' . $i . '.label') ? old('recipes.' . $i . '.label') : ($character->recipes && $character->recipes->get($i) ? $character->recipes->get($i)->name : '') }}" style="display:none;">
                                        <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                        <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('recipes.' . $i . '.label') ? old('recipes.' . $i . '.label') : ($character->recipes && $character->recipes->get($i) ? $character->recipes->get($i)->name : '') }}</span>&nbsp;
                                    </li>
                                    @if ($errors->has('recipes.' . $i . '.item_id'))
                                        <li class="'text-danger font-weight-bold'">
                                            {{ $errors->first('recipes.' . $i . '.item_id') }}
                                        </li>
                                    @endif
                                @endfor
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label for="public_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                Public Note
                                <small class="text-muted">anyone in the guild can see this</small>
                            </label>
                            <textarea data-max-length="144" name="public_note" rows="2" placeholder="anyone in the guild can see this" class="form-control">{{ old('public_note') ? old('public_note') : ($character ? $character->public_note : '') }}</textarea>
                        </div>
                    </div>

                    @if ($showOfficerNote)
                        <div class="col-12 mb-4">
                            <div class="form-group">
                                <label for="officer_note" class="font-weight-bold">
                                    <span class="text-muted fas fa-fw fa-shield"></span>
                                    Officer Note
                                    <small class="text-muted">only officers can see this</small>
                                </label>
                                <textarea data-max-length="144" name="officer_note" rows="2" placeholder="only officers can see this" class="form-control">{{ old('officer_note') ? old('officer_note') : ($character ? $character->officer_note : '') }}</textarea>
                            </div>
                        </div>
                    @endif

                    {{-- cut feature
                        <div class="col-12">
                            <div class="form-group">
                                <label for="personal_note" class="font-weight-bold">
                                    <span class="text-muted fas fa-fw fa-eye-slash"></span>
                                    Personal Note
                                    <small class="text-muted">only you can see this</small>
                                </label>
                                <textarea data-max-length="2000" name="personal_note" rows="2" placeholder="only you can see this" class="form-control">{{ old('personal_note') ? old('personal_note') : ($character ? $character->personal_note : '') }}</textarea>
                            </div>
                        </div>
                    --}}
                </div>

                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
