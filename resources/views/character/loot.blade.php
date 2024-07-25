@extends('layouts.app')
@section('title', __("Loot for") . " " . $character->name . " - " . config('app.name'))

@php
    $maxDate = (new \DateTime())->modify('+1 day')->format('Y-m-d');

    // Iterating over 100+ raids on 100+ items results in TENS OF THOUSANDS OF ITERATIONS.
    // So we're iterating over the raids only one time, saving the results, and printing them.
    $raidSelectOptions = (string)View::make('partials.raidOptions', ['raids' => $guild->raids]);
@endphp

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        @if (!request()->get('hideAds'))
            <div class="col-lg-2 d-lg-block d-none m-0 p-0">
                @include('partials/adLeftBanner')
            </div>
        @endif
        <div class="col-12 {{ request()->get('hideAds') ? 'col-xl-8 offset-xl-2 col-lg-10 offset-lg-1' : 'col-xl-8 col-lg-10 offset-lg-0' }}">
            <div class="row mb-3">
                <div class="col-12 pt-2 bg-lightest rounded">
                    @include('character/partials/header', ['headerSize' => 1, 'showEdit' => true, 'showLogs' => true, 'titleSuffix' => __("'s loot")])
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

            @include('partials/loadingBars')

            <form
                id="itemForm"
                style="display:none;"
                class="form-horizontal"
                role="form"
                method="POST"
                action="{{ route('character.updateLoot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                <fieldset>
                    {{ csrf_field() }}

                    <input hidden name="id" value="{{ $character->id }}" />
                    <input hidden name="wishlist_number" value="{{ $wishlistNumber }}" />

                    @if ($showPrios && !$guild->is_prio_disabled)
                        <div class="row mb-3 pt-2 bg-light rounded">
                            <div class="col-12 mb-2">
                                <span class="text-gold font-weight-bold">
                                    <span class="fas fa-fw fa-sort-amount-down"></span>
                                    {{ __("Prios") }}
                                </span>
                            </div>
                            <div class="col-12 pb-3">
                                @if ($character->prios->count() > 0)
                                    <ol class="lesser-indent">
                                        @foreach ($character->prios as $item)
                                            <li value="{{ $item->pivot->order }}">
                                                @include('partials/item', [
                                                    'wowheadLink'   => false,
                                                    'itemDate'      => $item->pivot->created_at,
                                                    'itemUsername'  => $item->added_by_username,
                                                    'showTier'      => true,
                                                    'strikeThrough' => $item->pivot->is_received,
                                                    'tierMode'      => $guild->tier_mode,
                                                ])
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

                    @if (!$guild->is_wishlist_disabled)
                        @php
                            $wishlistLockedExceptions = $guild->getWishlistLockedExceptions();
                            $wishlistNames = $guild->getWishlistNames();
                        @endphp

                        @if ($character->wishlist->count() > 6)
                            <div class="form-group">
                                <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Save") }}</button>
                            </div>
                        @endif

                        <div class="row mb-3 pt-2 bg-light rounded">
                            <div class="form-group mb-2 col-md-8 col-sm-10 col-12">

                                <!-- UPGRADES IMPORT START -->
                                <div>
                                    <span id="toggleImport" class="js-toggle-import btn btn-primary mt-2 mb-3">
                                        <span class="fas fa-fw fa-file-import"></span>
                                        @if ($guild->expansion_id === 1 || $guild->expansion_id === 4)
                                            {{ __("Import wishlist from sixtyupgrades.com") }} {{ __("OR WoWSims") }}
                                        @elseif ($guild->expansion_id === 2)
                                            {{ __("Import wishlist from sixtyupgrades.com") }} {{ __("OR WoWSims") }}
                                        @elseif ($guild->expansion_id === 3)
                                            {{ __("Import wishlist from sixtyupgrades.com") }} {{ __("OR WoWSims") }}
                                        @elseif ($guild->expansion_id === 5)
                                            {{ __("Import wishlist from sixtyupgrades.com") }} {{ __("OR WoWSims") }}
                                        @endif
                                    </span>
                                </div>

                                <div id="importArea" style="display:none;" class="mt-2 mb-3">
                                    <div class="mb-3">
                                        <span class="fas fa-fw fa-link"></span>
                                        @if ($guild->expansion_id === 1 || $guild->expansion_id === 4)
                                            <a href="https://sixtyupgrades.com/era/" target="_blank">
                                                sixtyupgrades.com
                                            </a>
                                            OR <a href="https://wowsims.github.io/" target="_blank">wowsims.github.io</a>
                                        @elseif ($guild->expansion_id === 2)
                                            <a href="https://sixtyupgrades.com/tbc/" target="_blank">
                                                sixtyupgrades.com
                                            </a>
                                            OR <a href="https://wowsims.github.io/tbc/" target="_blank">wowsims.github.io</a>
                                        @elseif ($guild->expansion_id === 3)
                                            <a href="https://sixtyupgrades.com/wotlk/" target="_blank">
                                                sixtyupgrades.com
                                            </a>
                                            OR <a href="https://wowsims.github.io/wotlk/" target="_blank">wowsims.github.io</a>
                                        @elseif ($guild->expansion_id === 4)
                                            <a href="https://sixtyupgrades.com/sod/" target="_blank">
                                                sixtyupgrades.com
                                            </a>
                                            OR <a href="https://wowsims.github.io/sod/" target="_blank">wowsims.github.io</a>
                                        @elseif ($guild->expansion_id === 5)
                                            <a href="https://sixtyupgrades.com/cata/" target="_blank">
                                                sixtyupgrades.com
                                            </a>
                                            OR <a href="https://wowsims.github.io/cata/" target="_blank">wowsims.github.io</a>
                                        @endif
                                    </div>

                                    <img
                                        src="{{ asset('images/upgrades_import_instructions.png') }}"
                                        alt="Export from 60/70/80/85upgrades.com, paste into the box below"
                                        class="pb-3 max-100"></img>

                                    <textarea
                                        id="importTextarea"
                                        name="import_textarea"
                                        autocomplete="off"
                                        rows="3"
                                        placeholder="{{ __('PASTE HERE') }}"
                                        class="form-control dark"></textarea>

                                    <div class="mt-3">
                                        <button disabled type="button" id="submitImport" class="btn btn-success">
                                            <span class="fas fa-fw fa-file-export"></span>
                                            {{ __("Import to wishlist") }}
                                        </button>
                                        <button type="button" class="js-toggle-import btn btn-primary">
                                            <span class="fas fa-fw fa-times-circle"></span>
                                            {{ __("Nevermind") }}
                                        </button>
                                        <div id="loading-indicator" class="mt-3 ml-5" style="display:none;">
                                            <div class="spinner-border" role="status">
                                              <span class="sr-only">{{ __("Loading") }}...</span>
                                            </div>
                                        </div>
                                        <div id="loaded-indicator" class="text-success font-weight-bold mt-3 ml-5" style="display:none;">
                                            {{ __("Finished") }}
                                        </div>
                                        <div id="status-message" class="mt-3 ml-5" style="display:none;">
                                        </div>
                                    </div>
                                </div>
                                <!-- UPGRADES IMPORT END -->

                                <div class="d-flex justify-content-between">
                                    <div>
                                        <label for="wishlist" class="sr-only">
                                            {{ __("Wishlist") }}
                                        </label>
                                        <div class="dropdown">
                                            <a class="dropdown-toggle font-weight-bold text-legendary" id="wishlistDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="fas fa-fw fa-scroll-old"></span>
                                                @if ($wishlistNames && $wishlistNames[$wishlistNumber - 1])
                                                    {{ $wishlistNames[$wishlistNumber - 1] }}
                                                @else
                                                    {{ __("Wishlist") }} {{ $wishlistNumber }}
                                                @endif

                                                @if ($guild->current_wishlist_number == $wishlistNumber)
                                                    <span class="text-success">{{ __('(active)') }}</span>
                                                @else
                                                    <span class="text-danger">{{ __('(inactive)') }}</span>
                                                @endif
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="wishlistDropdown">
                                                <a class="dropdown-item disabled" href="#" tabindex="-1" aria-disabled="true">
                                                    {{ __("active/locked is controlled by GM") }}
                                                    <br>
                                                    @if ($character->member && $character->member->is_wishlist_unlocked)
                                                        {{ __("officers have unlocked your wishlists") }}
                                                    @endif
                                                </a>
                                                @for ($i = 1; $i <= $maxWishlistLists; $i++)
                                                    <a class="dropdown-item"
                                                        href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug, 'wishlist_number' => $i]) }}">
                                                        @if ($wishlistNames && $wishlistNames[$i - 1])
                                                            {{ $wishlistNames[$i - 1] }}
                                                        @else
                                                            {{ __("Wishlist") }} {{ $i }}
                                                        @endif

                                                        @if ($guild->current_wishlist_number == $i)
                                                            <span class="text-success">{{ __('(active)') }}</span>
                                                        @else
                                                            <span class="text-danger">{{ __('(inactive)') }}</span>
                                                        @endif
                                                        @if ($guild->is_wishlist_locked)
                                                            @if (in_array($i, $wishlistLockedExceptions) || ($character->member && $character->member->is_wishlist_unlocked))
                                                                <span class="text-gold">{{ __('(unlocked)') }}</span>
                                                            @else
                                                                <span class="text-muted">{{ __('(locked)') }}</span>
                                                            @endif
                                                        @endif
                                                    </a>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <span id="clearWishlist" class="pl-2 small text-link cursor-pointer">
                                            <span class="fas fa-fw fa-trash"></span>
                                            {{ __("Clear wishlist") }}
                                        </span>
                                    </div>
                                </div>

                                @if ($lockWishlist)
                                    <small class="text-warning font-weight-normal">{{ __("locked by your guild master(s)") }}</small>
                                    <small class="text-muted">{{ __('except list(s)') }} {{ str_replace(',', ', ', $guild->wishlist_locked_exceptions) }}</small>
                                    <small class="text-muted font-weight-normal">&sdot; {{ __("max") }} {{ $maxWishlistItems }}</small>
                                @elseif (!$unlockWishlist && $guild->is_wishlist_locked)
                                    <small class="text-warning font-weight-normal">{{ __("locked for raiders") }}</small>
                                    <small class="text-muted">{{ __('except list(s)') }} {{ str_replace(',', ', ', $guild->wishlist_locked_exceptions) }}</small>
                                    <small class="text-muted font-weight-normal">&sdot; {{ __("max") }} {{ $maxWishlistItems }}</small>
                                @else
                                    <small class="text-muted font-weight-normal">{{ __("max") }} {{ $maxWishlistItems }}</small>
                                @endif
                                <small class="text-muted font-weight-normal">&sdot;</small>
                                <a href="{{ route('guild.loot.wishlist', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="small font-weight-normal">
                                    {{ __("see what other people wishlisted") }}
                                </a>

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
                                        <input id="wishlist" maxlength="40" data-max-length="40" type="text" placeholder="{{ __('type an item name') }}" class="js-item-autocomplete js-input-text form-control dark">
                                        <span class="js-loading-indicator" style="display:none;">{{ __("Searching...") }}</span>&nbsp;

                                        <ul id="wishlistItems" class="js-sortable-lazy no-bullet no-indent mb-0 bg-light">
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
                                                <li class="input-item position-relative {{ $itemId ? 'd-flex' : '' }} {{ $errors->has('wishlist.' . $i . '.item_id') ? 'text-danger font-weight-bold' : '' }}"
                                                    style="{{ $itemId ? '' : 'display:none;' }}">
                                                    <input type="checkbox" checked name="wishlist[{{ $i }}][item_id]" value="{{ $itemId }}" style="display:none;" />
                                                    <input type="checkbox" checked name="wishlist[{{ $i }}][label]" value="{{ $itemLabel }}" style="display:none;" />
                                                    <input type="checkbox" checked name="wishlist[{{ $i }}][pivot_id]" value="{{ $item ? $item->pivot->id : '' }}" style="display:none;"/>

                                                    <button type="button" class="js-input-button close close-top-right text-unselectable" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>

                                                    <div class="js-sort-handle move-cursor d-flex text-4 text-unselectable mr-1">
                                                        <div class="justify-content-center align-self-center">
                                                            <span class="fas fa-fw fa-grip-vertical text-muted"></span>
                                                        </div>
                                                    </div>

                                                    <div class="js-input-label mr-2" style="flex-grow: 1;">
                                                        <span class="js-item-display">
                                                            @includeWhen($itemId, 'partials/item', [
                                                                'wowheadLink'   => false,
                                                                'targetBlank'   => true,
                                                                'itemId'        => $itemId,
                                                                'itemName'      => $itemLabel,
                                                                'itemDate'      => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                                                                'itemUsername'  => ($item ? $item->added_by_username : null),
                                                                'showTier'      => true,
                                                                'strikeThrough' => ($item ? $item->pivot->is_received : null),
                                                                'tierMode'      => $guild->tier_mode,
                                                            ])
                                                            @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                                        </span>
                                                        @include('character/partials/itemEdit', ['name' => 'wishlist', 'index' => $i])
                                                    </div>
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
                    @endif

                    @if ($character->received->count() > 6)
                        <div class="form-group">
                            <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Save") }}</button>
                        </div>
                    @endif

                    <div class="row mb-3 pt-2 bg-light rounded">
                        <div class="form-group mb-2 col-md-8 col-sm-10 col-12">
                            <label for="received">
                                <span class="font-weight-bold text-success">
                                    <span class="fas fa-fw fa-sack"></span>
                                    {{ __("Loot Received") }}
                                </span>

                                @if ($lockReceived)
                                    <small class="text-warning font-weight-normal">{{ __("locked by your guild master(s)") }}</small>
                                @elseif (!$unlockReceived && $guild->is_received_locked)
                                    <small class="text-warning font-weight-normal">{{ __("locked for raiders") }}</small> <small class="text-muted font-weight-normal">{{ __("max") }} {{ $maxReceivedItems }}</small>
                                @else
                                    <small class="text-muted font-weight-normal">{{ __("max") }} {{ $maxReceivedItems }}</small>
                                @endif
                            </label>

                            @if (!$lockReceived)
                                <div class="form-group mb-0">
                                    <div class="checkbox">
                                        <label>
                                            <input checked type="checkbox" name="mark_as_received" value="1" class="" autocomplete="off">
                                                <small class="text-muted">{{ __("when adding items, mark prios and wishlist as received") }}</small>
                                        </label>
                                    </div>
                                </div>
                            @endif

                            @if ($lockReceived)
                                @if ($character->received->count() > 0)
                                    <ul class="lesser-indent no-bullet">
                                        @foreach ($character->received as $item)
                                            <li class="mb-2" value="{{ $item->pivot->order ? $item->pivot->order : '' }}">
                                                @include('partials/item', [
                                                    'wowheadLink'  => false,
                                                    'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                                                    'itemUsername' => $item->added_by_username,
                                                    'showTier'     => true,
                                                    'tierMode'     => $guild->tier_mode,
                                                ])
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
                                    <input id="received" maxlength="40" data-max-length="40" type="text" placeholder="{{ __('type an item name') }}" class="js-item-autocomplete js-input-text form-control dark">
                                    <span class="js-loading-indicator" style="display:none;">{{ __("Searching...") }}</span>&nbsp;

                                    <ul class="js-sortable-lazy no-bullet no-indent mb-0 bg-light">
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
                                            <li class="input-item position-relative {{ $itemId ? 'd-flex' : '' }} {{ $errors->has('received.' . $i . '.item_id') ? 'text-danger font-weight-bold' : '' }}"
                                                style="{{ $itemId ? '' : 'display:none;' }}">
                                                <input type="checkbox" checked name="received[{{ $i }}][item_id]" value="{{ $itemId }}" style="display:none;" />
                                                <input type="checkbox" checked name="received[{{ $i }}][label]" value="{{ $itemLabel }}" style="display:none;" />
                                                <input type="checkbox" checked name="received[{{ $i }}][pivot_id]" value="{{ $item ? $item->pivot->id : '' }}" style="display:none;"/>

                                                <button type="button" class="js-input-button close close-top-right text-unselectable" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>

                                                <div class="js-sort-handle move-cursor d-flex text-4 text-unselectable mr-1">
                                                    <div class="justify-content-center align-self-center">
                                                        <span class="fas fa-fw fa-grip-vertical text-muted"></span>
                                                    </div>
                                                </div>

                                                <div class="js-input-label">
                                                    <span class="js-item-display">
                                                        @includeWhen($itemId, 'partials/item', [
                                                            'wowheadLink'   => false,
                                                            'targetBlank'   => true,
                                                            'itemId'        => $itemId,
                                                            'itemName'      => $itemLabel,
                                                            'itemDate'      => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                                                            'itemUsername'  => ($item ? $item->added_by_username : null),
                                                            'showTier'      => true,
                                                            'strikeThrough' => ($item ? $item->pivot->is_received : null),
                                                            'tierMode'      => $guild->tier_mode,
                                                        ])
                                                        @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                                    </span>
                                                    @include('character/partials/itemEdit', ['name' => 'received', 'index' => $i])
                                                </div>
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

                    @if ($character->recipes->count() > 6)
                        <div class="form-group">
                            <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Save") }}</button>
                        </div>
                    @endif

                    <div class="row mb-3 pt-2 bg-light rounded">
                        <div class="form-group mb-2 col-md-8 col-sm-10 col-12">
                            <label for="recipes">
                                <span class="font-weight-bold text-rare text-rare">
                                    <span class="fas fa-fw fa-book"></span>
                                    {{ __("Rare Recipes") }}
                                </span>
                                <small class="text-muted font-weight-normal" title="Max {{ $maxRecipes }}">{{ __("so your guildmates can see") }} &sdot;</small>
                                <a href="{{ route('guild.recipe.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="small font-weight-normal">
                                    {{ __("view guild recipes") }}
                                </a>
                            </label>

                            <div class="{{ $errors->has('recipes.*') ? 'has-error' : '' }}">
                                <input id="recipes" maxlength="40" data-max-length="40" type="text" placeholder="{{ __('type an item name') }}" class="js-item-autocomplete js-input-text form-control dark">
                                <span class="js-loading-indicator" style="display:none;">{{ __("Searching...") }}</span>&nbsp;

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
                                        <li class="input-item position-relative {{ $itemId ? 'd-flex' : '' }} {{ $errors->has('recipes.' . $i . '.item_id') ? 'text-danger font-weight-bold' : '' }}"
                                            style="{{ $itemId ? '' : 'display:none;' }}">
                                            <input type="checkbox" checked name="recipes[{{ $i }}][item_id]" value="{{ $itemId }}" style="display:none;">
                                            <input type="checkbox" checked name="recipes[{{ $i }}][label]" value="{{ $itemLabel }}" style="display:none;">
                                            <input type="checkbox" checked name="recipes[{{ $i }}][pivot_id]" value="{{ $item ? $item->pivot->id : '' }}" style="display:none;"/>

                                            <button type="button" class="js-input-button close close-top-right text-unselectable" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>

                                            <div class="js-sort-handle move-cursor text-4 text-unselectable d-flex mr-1">
                                                <div class="justify-content-center align-self-center">
                                                    <span class="fas fa-fw fa-grip-vertical text-muted"></span>
                                                </div>
                                            </div>

                                            <span class="js-input-label">
                                                <span class="js-item-display">
                                                    @includeWhen($itemId, 'partials/item', [
                                                        'wowheadLink'  => false,
                                                        'targetBlank'  => true,
                                                        'itemId'       => $itemId,
                                                        'itemName'     => $itemLabel,
                                                        'itemDate'     => ($item ? $item->pivot->created_at : null),
                                                        'itemUsername' => ($item ? $item->added_by_username : null),
                                                        'showTier'     => true,
                                                        'tierMode'     => $guild->tier_mode,
                                                    ])
                                                    @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                                                </span>
                                            </span>
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
                                    {{ __("Public Note") }}
                                    <small class="text-muted">{{ __("anyone in the guild can see this") }}</small>
                                </label>
                                <textarea maxlength="140" data-max-length="140" name="public_note" rows="2" placeholder="{{ __('anyone in the guild can see this') }}" class="form-control dark">{{ old('public_note') ? old('public_note') : ($character ? $character->public_note : '') }}</textarea>
                            </div>
                        </div>

                        @if ($showOfficerNote)
                            <div class="col-12 mb-4">
                                <div class="form-group">
                                    <label for="officer_note" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-shield"></span>
                                        {{ __("Officer Note") }}
                                        <small class="text-muted">{{ __("only officers can see this") }}</small>
                                    </label>
                                    <textarea maxlength="140" data-max-length="140" name="officer_note" rows="2" placeholder="{{ __('only officers can see this') }}" class="form-control dark">{{ old('officer_note') ? old('officer_note') : ($character ? $character->officer_note : '') }}</textarea>
                                </div>
                            </div>
                        @endif

                        {{-- cut feature
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="personal_note" class="font-weight-bold">
                                        <span class="text-muted fas fa-fw fa-eye-slash"></span>
                                        {{ __("Personal Note") }}
                                        <small class="text-muted">{{ __("only you can see this") }}</small>
                                    </label>
                                    <textarea maxlength="2000" data-max-length="2000" name="personal_note" rows="2" placeholder="{{ __('only you can see this') }}" class="form-control dark">{{ old('personal_note') ? old('personal_note') : ($character ? $character->personal_note : '') }}</textarea>
                                </div>
                            </div>
                        --}}
                    </div>

                    <div class="form-group">
                        <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> {{ __("Save") }}</button>
                    </div>
                </fieldset>
            </form>
        </div>
        @if (!request()->get('hideAds'))
            <div class="col-xl-2 d-lg-block d-none m-0 p-0">
                @include('partials/adRightBanner')
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    var guild = {!! $guild->toJson() !!};
    var maxItems = {{ $maxWishlistItems }};
</script>
<script src="{{ loadScript('characterLootEdit.js') }}"></script>
@endsection
