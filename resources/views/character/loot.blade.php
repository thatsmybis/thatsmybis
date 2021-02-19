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

            @if (count($errors) > 0)
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <form class="form-horizontal" role="form" method="POST" action="{{ route('character.updateLoot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $character->id }}" />

                @if ($showPrios)
                    <div class="row mb-3 pt-2 bg-light rounded">
                        <div class="col-12 mb-2">
                            <span class="text-gold font-weight-bold">
                                <span class="fas fa-fw fa-sort-amount-down"></span>
                                Prio's
                            </span>
                        </div>
                        <div class="col-12 pb-3">
                            @if ($character->prios->count() > 0)
                                <ol class="lesser-indent">
                                    @foreach ($character->prios as $item)
                                        <li value="{{ $item->pivot->order }}">
                                            @include('partials/item', ['wowheadLink' => false, 'itemDate' => $item->pivot->created_at, 'itemUsername' => $item->added_by_username, 'strikeThrough' => $item->pivot->is_received])
                                            @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <div class="pl-4">
                                    —
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12 mb-3">
                        <small>
                            <span class="font-weight-bold">Hint:</span> Your top 4 items are displayed first; make them count
                        </small>
                    </div>
                </div>

                <div class="row mb-3 pt-2 bg-light rounded">
                    <div class="form-group mb-2 col-md-8 col-sm-10 col-12">
                        <label for="wishlist">
                            <span class="font-weight-bold text-legendary">
                                <span class="fas fa-fw fa-scroll-old"></span>
                                Wishlist
                            </span>
                            @if ($lockWishlist)
                                <small class="text-warning font-weight-normal">locked by your guild master(s)</small>
                            @elseif (!$unlockWishlist && $guild->is_wishlist_locked)
                                <small class="text-warning font-weight-normal">locked for raiders</small> <small class="text-muted font-weight-normal">max {{ $maxWishlistItems }}</small>
                            @else
                                <small class="text-muted font-weight-normal">max {{ $maxWishlistItems }}</small>
                            @endif
                        </label>

                        @if ($lockWishlist)
                            @if ($character->wishlist->count() > 0)
                                <ol class="lesser-indent">
                                    @foreach ($character->wishlist as $item)
                                        <li class="mb-2" value="{{ $item->pivot->order }}">
                                            @include('partials/item', ['wowheadLink' => false, 'itemDate' => $item->pivot->created_at, 'itemUsername' => $item->added_by_username, 'strikeThrough' => $item->pivot->is_received])
                                            @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <div class="pl-4">
                                    —
                                </div>
                            @endif
                        @else
                            <div class="{{ $errors->has('wishlist.*') ? 'has-error' : '' }}">
                                <input id="wishlist" maxlength="40" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text form-control dark">
                                <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                <ul class="js-sortable no-bullet no-indent mb-0">
                                    @for ($i = 0; $i < $maxWishlistItems; $i++)
                                        @php
                                            $item      = null;
                                            $itemId    = null;
                                            $itemLabel = null;

                                            if (old('wishlist.' . $i . '.item_id')) {
                                                $itemId = old('wishlist.' . $i . '.item_id');
                                                if (old('wishlist.' . $i . '.label')) {
                                                    $itemLabel = old('wishlist.' . $i . '.label');
                                                } else {
                                                    $itemLabel = $itemId;
                                                }
                                            } else if ($character->wishlist && $character->wishlist->get($i)) {
                                                $item      = $character->wishlist->get($i);
                                                $itemId    = $item->item_id;
                                                $itemLabel = $item->name;
                                            }
                                        @endphp
                                        <li class="input-item {{ $errors->has('wishlist.' . $i . '.item_id') ? 'text-danger font-weight-bold' : '' }}"
                                            style="{{ $itemId ? '' : 'display:none;' }}">
                                            <input hidden name="wishlist[{{ $i }}][pivot_id]" value="{{ $item ? $item->pivot->id : '' }}" />
                                            <input type="checkbox" checked name="wishlist[{{ $i }}][item_id]" value="{{ $itemId }}" style="display:none;">
                                            <input type="checkbox" checked name="wishlist[{{ $i }}][label]" value="{{ $itemLabel }}" style="display:none;">
                                            <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                            <span class="js-sort-handle js-input-label move-cursor text-unselectable d-inline-block">
                                                @includeWhen($itemId, 'partials/item', ['wowheadLink' => false, 'targetBlank' => true, 'itemId' => $itemId, 'itemName' => $itemLabel, 'itemDate' => ($item ? $item->pivot->created_at : null), 'itemUsername' => ($item ? $item->added_by_username : null), 'strikeThrough' => ($item ? $item->pivot->is_received : null)])
                                                @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                                @include('character/partials/itemEdit', ['name' => 'wishlist', 'index' => $i])
                                            </span>&nbsp;
                                        </li>
                                        @if ($errors->has('wishlist.' . $i . '.item_id'))
                                            <li class="'text-danger font-weight-bold'">
                                                {{ $errors->first('wishlist.' . $i . '.item_id') }}
                                            </li>
                                        @endif
                                    @endfor
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mb-3 pt-2 bg-light rounded">
                    <div class="form-group mb-2 col-md-8 col-sm-10 col-12">
                        <label for="received">
                            <span class="font-weight-bold text-success">
                                <span class="fas fa-fw fa-sack"></span>
                                Loot Received
                            </span>

                            @if ($lockReceived)
                                <small class="text-warning font-weight-normal">locked by your guild master(s)</small>
                            @elseif (!$unlockReceived && $guild->is_received_locked)
                                <small class="text-warning font-weight-normal">locked for raiders</small> <small class="text-muted font-weight-normal">max {{ $maxReceivedItems }}</small>
                            @else
                                <small class="text-muted font-weight-normal">max {{ $maxReceivedItems }}</small>
                            @endif
                        </label>

                        @if ($lockReceived)
                            @if ($character->received->count() > 0)
                                <ul class="lesser-indent no-bullet">
                                    @foreach ($character->received as $item)
                                        <li class="mb-2" value="{{ $item->pivot->order ? $item->pivot->order : '' }}">
                                            @include('partials/item', ['wowheadLink' => false, 'itemDate' => $item->pivot->created_at, 'itemUsername' => $item->added_by_username])
                                            @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="pl-4">
                                    —
                                </div>
                            @endif
                        @else
                            <div class="{{ $errors->has('received.*') ? 'has-error' : '' }}">
                                <input id="received" maxlength="40" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text form-control dark">
                                <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                                <ul class="js-sortable no-bullet no-indent mb-0">
                                    @for ($i = 0; $i < $maxReceivedItems; $i++)
                                        @php
                                            $item      = null;
                                            $itemId    = null;
                                            $itemLabel = null;

                                            if (old('received.' . $i . '.item_id')) {
                                                $itemId = old('received.' . $i . '.item_id');
                                                if (old('received.' . $i . '.label')) {
                                                    $itemLabel = old('received.' . $i . '.label');
                                                } else {
                                                    $itemLabel = $itemId;
                                                }
                                            } else if ($character->received && $character->received->get($i)) {
                                                $item      = $character->received->get($i);
                                                $itemId    = $item->item_id;
                                                $itemLabel = $item->name;
                                            }
                                        @endphp
                                        <li class="input-item {{ $errors->has('received.' . $i . '.item_id') ? 'text-danger font-weight-bold' : '' }}" style="{{ $itemId ? '' : 'display:none;' }}">
                                            <input hidden name="received[{{ $i }}][pivot_id]" value="{{ $item ? $item->pivot->id : '' }}" />
                                            <input type="checkbox" checked name="received[{{ $i }}][item_id]" value="{{ $itemId }}" style="display:none;">
                                            <input type="checkbox" checked name="received[{{ $i }}][label]" value="{{ $itemLabel }}" style="display:none;">
                                            <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                            <span class="js-sort-handle js-input-label move-cursor text-unselectable d-inline-block">
                                                @includeWhen($itemId, 'partials/item', ['wowheadLink' => false, 'targetBlank' => true, 'itemId' => $itemId, 'itemName' => $itemLabel, 'itemDate' => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null), 'itemUsername' => ($item ? $item->added_by_username : null)])
                                                @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                            </span>&nbsp;
                                        </li>
                                        @if ($errors->has('received.' . $i . '.item_id'))
                                            <li class="'text-danger font-weight-bold'">
                                                {{ $errors->first('received.' . $i . '.item_id') }}
                                            </li>
                                        @endif
                                    @endfor
                                </ul>
                            </div>
                        @endif
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
                            <input id="recipes" maxlength="40" data-max-length="40" type="text" placeholder="type an item name" class="js-item-autocomplete js-input-text form-control dark">
                            <span class="js-loading-indicator" style="display:none;">Searching...</span>&nbsp;

                            <ul class="js-sortable no-bullet no-indent mb-0">
                                @for ($i = 0; $i < $maxRecipes; $i++)
                                    @php
                                        $item      = null;
                                        $itemId    = null;
                                        $itemLabel = null;

                                        if (old('recipes.' . $i . '.item_id')) {
                                            $itemId = old('recipes.' . $i . '.item_id');
                                            if (old('recipes.' . $i . '.label')) {
                                                $itemLabel = old('recipes.' . $i . '.label');
                                            } else {
                                                $itemLabel = $itemId;
                                            }
                                        } else if ($character->recipes && $character->recipes->get($i)) {
                                            $item      = $character->recipes->get($i);
                                            $itemId    = $item->item_id;
                                            $itemLabel = $item->name;
                                        }
                                    @endphp
                                    <li class="input-item {{ $errors->has('recipes.' . $i . '.item_id') ? 'text-danger font-weight-bold' : '' }}" style="{{ $itemId ? '' : 'display:none;' }}">
                                        <input hidden name="recipes[{{ $i }}][pivot_id]" value="{{ $item ? $item->pivot->id : '' }}" />
                                        <input type="checkbox" checked name="recipes[{{ $i }}][item_id]" value="{{ $itemId }}" style="display:none;">
                                        <input type="checkbox" checked name="recipes[{{ $i }}][label]" value="{{ $itemLabel }}" style="display:none;">
                                        <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                                        <span class="js-sort-handle js-input-label move-cursor text-unselectable d-inline-block">
                                            @includeWhen($itemId, 'partials/item', ['wowheadLink' => false, 'targetBlank' => true, 'itemId' => $itemId, 'itemName' => $itemLabel, 'itemDate' => ($item ? $item->pivot->created_at : null), 'itemUsername' => ($item ? $item->added_by_username : null)])
                                            @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                        </span>&nbsp;
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
                            <textarea maxlength="140" data-max-length="140" name="public_note" rows="2" placeholder="anyone in the guild can see this" class="form-control dark">{{ old('public_note') ? old('public_note') : ($character ? $character->public_note : '') }}</textarea>
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
                                <textarea maxlength="140" data-max-length="140" name="officer_note" rows="2" placeholder="only officers can see this" class="form-control dark">{{ old('officer_note') ? old('officer_note') : ($character ? $character->officer_note : '') }}</textarea>
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
                                <textarea maxlength="2000" data-max-length="2000" name="personal_note" rows="2" placeholder="only you can see this" class="form-control dark">{{ old('personal_note') ? old('personal_note') : ($character ? $character->personal_note : '') }}</textarea>
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

@section('scripts')
<script>
    var guild = {!! $guild->toJson() !!};
</script>
@endsection
