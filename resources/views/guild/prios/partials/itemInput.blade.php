<div class="row striped-light pb-2 pt-3 rounded">

    <input hidden name="items[{{ $item->item_id }}][item_id]" value="{{ $item->item_id }}">

    <div class="col-lg-4 col-12">
        <div class="d-inline-grid align-middle text-5 mb-2">
            <label for="items[{{ $item->item_id }}][name]" class="font-weight-bold d-none d-sm-block">
                <span class="sr-only">
                    Item Name
                </span>
            </label>
            @include('partials/item', [
                'wowheadLink' => false,
                'targetBlank' => true,
                'showTier'    => true,
                'tierMode'    => $guild->tier_mode,
                ])
        </div>
        @if ($item->childItems->count())
            <ul class="ml-3 small list-inline">
                @foreach ($item->childItems as $childItem)
                    <li class="list-inline-item">
                        @include('partials/item', [
                            'item' => $childItem,
                            'iconSize' => 'small',
                            'wowheadLink' => false,
                            'targetBlank' => true,
                        ])
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="col-lg-5 col-12">
        <label for="items[details]" class="font-weight-bold {{ $loop->iteration > 1 ? 'd-none' : '' }}">
            @if ($loop->first)
                Notes
            @else
                <span class="sr-only">Notes</span>
            @endif
        </label>
        <ul class="fa-ul">
            @if ($item->guild_note)
                <li title="Note">
                    <span class="fa-li"><span class="fal fa-fw fa-sticky-note text-muted"></span></span>
                    <span class="js-markdown-inline">{{ $item->guild_note }}</span>
                </li>
            @endif

            @if ($item->guild_priority)
                <li title="Priority note">
                    <span class="fa-li"><span class="fal fa-fw fa-sort-amount-down text-muted"></span></span>
                    <span class="js-markdown-inline">{{ $item->guild_priority }}</span>
                </li>
            @endif

            @if (!$guild->is_wishlist_disabled && (($guild->is_attendance_hidden && $item->wishlistCharacters->count() > 0) || (!$guild->is_attendance_hidden && $item->wishlistCharactersWithAttendance->count() > 0)))
                <li>
                    <span class="fa-li mt-1" title="Characters who have it wishlisted"><span class="fal fa-fw fa-scroll-old text-legendary"></span></span>
                    <ul class="list-inline">
                        @foreach (($guild->is_attendance_hidden ? $item->wishlistCharacters : $item->wishlistCharactersWithAttendance) as $character)
                            <li class="list-inline-item">
                                <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}"
                                    class="tag text-{{ strtolower($character->class) }} {{ $character->pivot->is_received ? 'font-strikethrough' : '' }}" target="_blank">
                                    <span class="text-muted">{{ $character->pivot->order ? $character->pivot->order : '' }}</span>
                                    <!--<span class="role-circle" style="background-color:{{ getHexColorFromDec($character->raid_group_color) }}"></span>-->
                                    <span class="text-muted small font-weight-bold">{{ $character->pivot->is_offspec ? 'OS' : '' }}</span>
                                    <span class="text-{{ strtolower($character->class) }}">{{ $character->name }}</span>
                                    @if (!$guild->is_attendance_hidden && (isset($character->attendance_percentage) || isset($character->raid_count)))
                                        <span class="small">
                                            @include('partials/attendanceTag', ['attendancePercentage' => $character->attendance_percentage, 'raidCount' => $character->raid_count, 'raidShort' => true])
                                        </span>
                                    @endif
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
                <li>
                    <span class="fa-li mt-1" title="Characters who have received it"><span class="fal fa-fw fa-sack text-success"></span></span>
                    <ul class="list-inline">
                        @foreach ($item->receivedAndRecipeCharacters as $character)
                            <li class="list-inline-item">
                                <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}"
                                    class="tag" target="_blank">
                                    <span class="text-{{ strtolower($character->class) }}">{{ $character->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
        </ul>
    </div>

    <div class="col-lg-3 col-12 {{ $errors->has('items.' . $item->item_id . '.*') ? 'bg-danger rounded font-weight-bold' : '' }}">
        <div class="form-group mb-2">
            <label for="items[{{ $item->item_id }}][characters]" class="font-weight-bold {{ $loop->iteration > 1 ? 'd-none' : '' }}">
                @if ($loop->first)
                    <span class="fas fa-fw fa-sort-amount-down text-muted"></span>
                    Prio'd Characters
                    <span class="text-muted font-weight-normal small">max {{ $maxPrios }}</span>
                @else
                    <span class="sr-only">Priority Characters (max {{ $maxPrios }})</span>
                @endif
            </label>

            <select id="{{ $loop->index }}"
                name=""
                class="js-input-select form-control dark selectpicker"
                data-live-search="true"
                data-input-prefix="items[{{ $item->item_id }}][characters]"
                data-input-key="[character_id]"
                data-no-dupes="1"
                autocomplete="off">
                <option value="">
                    —
                </option>

                {{-- See the notes at the top for why the options look like this --}}
                @if (old('items.' . $item->item_id . '.character_id'))
                    @php
                        // Select the correct option
                        $options = str_replace('hack="' . old('items.' . $item->item_id . '.character_id') . '"', 'selected', $characterSelectOptions);
                     @endphp
                     {!! $options !!}
                @else
                    {!! $characterSelectOptions !!}
                @endif
            </select>

            <ol class="js-sortable-lazy numbered no-indent mt-3 mb-0">
                @for ($i = 0; $i < $maxPrios; $i++)
                    @php
                        $oldInputName   = 'items.' . $item->item_id . '.characters.' . $i;
                        $character      = $item->priodCharacters->get($i) ? $item->priodCharacters->get($i) : null;
                        $characterId    = old($oldInputName . '.character_id') ? old($oldInputName . '.character_id') : ($character ? $character->id : null);
                        $strikeThrough = !old($oldInputName) && $character && $character->pivot->is_received ? 'font-strikethrough' : null;
                    @endphp
                    <li class="input-item position-relative {{ $characterId ? 'd-flex' : '' }} {{ $errors->has('items.' . $item->item_id . '.characters.' . $i ) ? 'text-danger font-weight-bold' : '' }} {{ $strikeThrough }}"
                        data-needs-template="{{ !$characterId ? '1' : '0' }}"
                        data-index="{{ $i }}"
                        data-input-prefix="items[{{ $item->item_id }}][characters][{{ $i }}]"
                        data-flex="1"
                        style="{{ $characterId ? '' : 'display:none;' }}">
                        @if ($characterId)
                            @php
                                $characterLabel = old($oldInputName . '.label') ? old($oldInputName . '.label') : ($character ? $character->name : null);
                                $characterOrder = old($oldInputName . '.order') ? old($oldInputName . '.order') : ($character && $character->pivot->order ? $character->pivot->order : null);
                                $isReceived     = old($oldInputName . '.is_received') && old($oldInputName . '.is_received') == 1 ? 'checked' : ($character && $character->pivot->is_received ? 'checked' : null);
                                $isOffspec      = old($oldInputName . '.is_offspec') && old($oldInputName . '.is_offspec') == 1 ? 'checked' : ($character && $character->pivot->is_offspec ? 'checked' : null);

                                // Let the order field auto-generate if it's not different from the index
                                if ($characterOrder && $characterOrder == $i + 1) {
                                    $characterOrder = null;
                                }
                            @endphp
                            <input type="checkbox" checked name="items[{{ $item->item_id }}][characters][{{ $i }}][character_id]" value="{{ $characterId }}" style="display:none;">
                            <input type="checkbox" checked name="items[{{ $item->item_id }}][characters][{{ $i }}][label]" value="{{ $characterLabel }}" style="display:none;">

                            <button type="button" class="js-input-button close close-top-right text-unselectable" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>

                            <div class="js-sort-handle d-flex move-cursor text-unselectable mr-1 text-4">
                                <div class="justify-content-center align-self-center">
                                    <span class="fas fa-fw fa-grip-vertical text-muted"></span>
                                </div>
                            </div>

                            <div class="">
                                <span class="js-input-label text-{{ $character ? strtolower($character->class) : '' }} font-weight-medium">
                                    {!! $characterLabel !!}
                                </span>

                                <ul class="list-inline">
                                    <li class="list-inline-item">
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <label class="sr-only" for="items[{{ $item->item_id }}][characters][{{ $i }}][order]">
                                                    Rank
                                                </label>
                                                &nbsp;
                                                <input name="items[{{ $item->item_id }}][characters][{{ $i }}][order]"
                                                    type="number"
                                                    min="1"
                                                    max="{{ $maxPrios }}"
                                                    class="d-inline numbered form-control dark order"
                                                    autocomplete="off"
                                                    placeholder="{{ $i + 1 }}"
                                                    value="{{ $characterOrder }}" />
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-inline-item">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="items[{{ $item->item_id }}][characters][{{ $i }}][is_offspec]" value="1" class="" autocomplete="off"
                                                    {{ $isOffspec }}>
                                                    Offspec
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-inline-item">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="items[{{ $item->item_id }}][characters][{{ $i }}][is_received]" value="1" class="" autocomplete="off"
                                                    {{ $isReceived }}>
                                                    Received
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </li>

                    @if ($errors->has('items.' . $item->item_id . '.characters.*'))
                        <li class="'text-danger font-weight-bold'">
                            {{ $errors->first('items.' . $item->item_id . '.characters.*') }}
                        </li>
                    @endif
                @endfor
            </ol>

            @if ($errors->has('items.' . $item->item_id . '.*'))
                <div class="'text-danger font-weight-bold'">
                    {{ $errors->first('items.' . $item->item_id . '.*') }}
                </div>
            @endif
        </div>
    </div>
</div>
