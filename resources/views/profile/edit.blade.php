@extends('layouts.app')
@section('title', "Edit " . $user->username . "'s Profile - " . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            @if (count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="alert alert-danger">
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <form class="form-horizontal" role="form" method="POST" action="{{ route('updateUser', $user->id) }}">
                {{ csrf_field() }}
                <div class="form-group mt-5">
                    <label for="username" class="font-weight-bold">
                        Main's Name
                    </label>
                    <small class="text-muted">
                        Your main's name
                    </small>
                    <input name="username" maxlength="255" type="text" class="form-control" placeholder="What's your name?" value="{{ old('username') ? old('username') : $user->username }}" />
                </div>


                <div class="form-group mt-5">
                    <label for="spec" class="font-weight-bold">
                        Spec
                    </label>
                    <small class="text-muted">
                        Your main's spec
                    </small>
                    <input name="spec" maxlength="50" type="text" class="form-control" placeholder="eg. Boomkin" value="{{ old('spec') ? old('spec') : $user->spec }}" />
                </div>

                <div class="form-group mt-5">
                    <label for="alts" class="font-weight-bold">
                        60 Alts
                    </label>
                    <small class="text-muted">
                        Only ones you can raid with
                    </small>

                    <?php
                    $altsArray = splitByLine($user->alts);
                    $altsLength = count($altsArray) - 1;
                    ?>

                    <div class="form-group">
                        <input name="alts[]" maxlength="50" type="text" class="form-control" placeholder="eg. 60 Undead Rogue"   value="{{ old('alts.0') ? old('alts.0') : ($altsLength >= 0 ? $altsArray[0] : '') }}" />
                    </div>
                    <div class="form-group">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Orc Hunter"     value="{{ old('alts.1') ? old('alts.1') : ($altsLength >= 1 ? $altsArray[1] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Undead Mage"    value="{{ old('alts.2') ? old('alts.2') : ($altsLength >= 2 ? $altsArray[2] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Tauren Druid"   value="{{ old('alts.3') ? old('alts.3') : ($altsLength >= 3 ? $altsArray[3] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Troll Priest"   value="{{ old('alts.4') ? old('alts.4') : ($altsLength >= 4 ? $altsArray[4] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Orc Warrior"    value="{{ old('alts.5') ? old('alts.5') : ($altsLength >= 5 ? $altsArray[5] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Undead Warlock" value="{{ old('alts.6') ? old('alts.6') : ($altsLength >= 6 ? $altsArray[6] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Tauren Shaman"  value="{{ old('alts.7') ? old('alts.7') : ($altsLength >= 7 ? $altsArray[7] : '') }}" />
                    </div>
                </div>

                <div class="form-group mt-5">
                    <label for="rank" class="font-weight-bold">
                        PvP Rank
                    </label>
                    <small class="text-muted">
                        Current rank
                    </small>
                    <input name="rank" maxlength="50" type="text" class="form-control" placeholder="eg. 2" value="{{ old('rank') ? old('rank') : $user->rank }}" />
                </div>

                <div class="form-group mt-5">
                    <label for="rank_goal" class="font-weight-bold">
                        PvP Rank Goal
                    </label>
                    <small class="text-muted">
                        Your expected rank
                    </small>
                    <input name="rank_goal" maxlength="50" type="text" class="form-control" placeholder="eg. 14" value="{{ old('rank_goal') ? old('rank_goal') : $user->rank_goal }}" />
                </div>

                <div class="form-group mt-5">
                    <div class="row">
                        <div class="col-md-8 col-sm-10 col-12">
                            <label for="wishlist">
                                Wishlist
                                <small class="text-muted font-weight-normal">Max {{ $maxWishlistItems }}</small>
                            </label>

                            <div class="{{ $errors->has('wishlist.*') ? 'has-error' : '' }}">
                                <input id="wishlist" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text form-control">
                                <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                <ul class="js-sortable no-bullet no-indent mb-0">
                                    @for ($i = 0; $i < $maxWishlistItems; $i++)
                                        <li class="input-item" style="{{ old('wishlist.' . $i) || ($user->wishlist && $user->wishlist->get($i)) ? '' : 'display:none;' }}">
                                            <input type="checkbox" checked name="wishlist[]" value="{{ old('wishlist.' . $i) ? old('wishlist.' . $i) : ($user->wishlist && $user->wishlist->get($i) ? $user->wishlist->get($i)->id : '') }}" style="display:none;">
                                            <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                            <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('wishlist.' . $i) ? old('wishlist.' . $i) : ($user->wishlist && $user->wishlist->get($i) ? $user->wishlist->get($i)->name : '') }}</span>&nbsp;
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

                <div class="form-group mt-5">
                    <div class="row">
                        <div class="col-md-8 col-sm-10 col-12">
                            <label for="received">
                                Loot Received
                                <small class="text-muted font-weight-normal">Max {{ $maxReceivedItems }}</small>
                            </label>

                            <div class="{{ $errors->has('received.*') ? 'has-error' : '' }}">
                                <input id="received" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text form-control">
                                <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                <ul class="js-sortable no-bullet no-indent mb-0">
                                    @for ($i = 0; $i < $maxReceivedItems; $i++)
                                        <li class="input-item" style="{{ old('received.' . $i) || ($user->received && $user->received->get($i)) ? '' : 'display:none;' }}">
                                            <input type="checkbox" checked name="received[]" value="{{ old('received.' . $i) ? old('received.' . $i) : ($user->received && $user->received->get($i) ? $user->received->get($i)->id : '') }}" style="display:none;">
                                            <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                            <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('received.' . $i) ? old('received.' . $i) : ($user->received && $user->received->get($i) ? $user->received->get($i)->name : '') }}</span>&nbsp;
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

                <div class="form-group mt-5">
                    <div class="row">
                        <div class="col-md-8 col-sm-10 col-12">
                            <label for="recipes">
                                Rare Recipes
                                <small class="text-muted font-weight-normal">Max {{ $maxRecipes }}</small>
                            </label>

                            <div class="{{ $errors->has('recipes.*') ? 'has-error' : '' }}">
                                <input id="recipes" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text form-control">
                                <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                <ul class="js-sortable no-bullet no-indent mb-0">
                                    @for ($i = 0; $i < $maxRecipes; $i++)
                                        <li class="input-item" style="{{ old('recipes.' . $i) || ($user->recipes && $user->recipes->get($i)) ? '' : 'display:none;' }}">
                                            <input type="checkbox" checked name="recipes[]" value="{{ old('recipes.' . $i) ? old('recipes.' . $i) : ($user->recipes && $user->recipes->get($i) ? $user->recipes->get($i)->id : '') }}" style="display:none;">
                                            <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                            <span class="js-sort-handle js-input-label move-cursor text-unselectable">{{ old('recipes.' . $i) ? old('recipes.' . $i) : ($user->recipes && $user->recipes->get($i) ? $user->recipes->get($i)->name : '') }}</span>&nbsp;
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

                <div class="form-group mt-5">
                    <label for="note" class="font-weight-bold">
                        Public Notes
                    </label>
                    <small class="text-muted">
                        ie. "Rushing for 8/8 tier 1"
                    </small>
                    <textarea name="note" rows="3" maxlength="1000" placeholder="" class="form-control">{{ old('note') ? old('note') : $user->note }}</textarea>
                </div>

                @if ($showOfficerNote)
                    <div class="form-group">
                        <label for="officer_note" class="font-weight-bold">
                            Officer Notes
                        </label>
                        <textarea name="officer_note" rows="3" maxlength="1000" placeholder="" class="form-control">{{ old('officer_note') ? old('officer_note') : $user->officer_note }}</textarea>
                    </div>
                @endif

                <div class="form-group">
                    <button class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $(".js-show-next").change(function() {
            showNext(this);
        }).change();

        $(".js-show-next").keyup(function() {
            showNext(this);
        });
    });

    // If the current element has a value, show it and the next element that is hidden because it is empty
    function showNext(currentElement) {
        if ($(currentElement).val() != "") {
            $(currentElement).show();
            $(currentElement).parent().next(".js-hide-empty").show();
        }
    }
</script>
@endsection
