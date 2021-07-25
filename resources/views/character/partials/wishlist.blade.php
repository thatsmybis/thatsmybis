<div class="col-12 mb-2">
    @if ($showEditLoot)
        <a href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug, 'wishlist_number' => $wishlistNumber]) }}">
            <span class="{{ $isActive ? 'text-legendary font-weight-bold' : 'text-danger' }}">
                <span class="fas fa-fw fa-scroll-old"></span>
                {{ __("Wishlist") }} {{ $wishlistNumber }} {{ $isActive ? '' : __('(inactive)') }}
            </span>
            <span class="small align-text- fas fa-fw fa-pencil"></span>
        </a>
    @else
        <span class="{{ $isActive ? 'text-legendary font-weight-bold' : 'text-danger' }}">
            <span class="fas fa-fw fa-scroll-old"></span>
            {{ __("Wishlist") }} {{ $wishlistNumber }} {{ $isActive ? '' : __('(inactive)') }}
        </span>
    @endif
    <span class="js-sort-wishlists text-link">
        <span class="fas fa-fw fa-exchange cursor-pointer"></span>
    </span>
</div>
<div class="col-12 pb-3">
    @if ($wishlist->count() > 0)
        <ol class="js-wishlist-sorted" style="{{ $guild->do_sort_items_by_instance ? '' : 'display:none;' }}">
            @php
                $lastInstanceId = null;
            @endphp
            @foreach ($wishlist->sortBy(function ($item) {return [$item->instance_order, $item->pivot->order]; }) as $item)
                @if ($item->instance_id != $lastInstanceId)
                    <li class="no-bullet no-indent {{ !$loop->first ? 'mt-3' : '' }}">
                        {{ $item->instance_name }}
                    </li>
                @endif

                <li value="{{ $item->pivot->order }}">
                    @include('partials/item', [
                        'wowheadLink'   => false,
                        'itemDate'      => $item->pivot->created_at,
                        'itemUsername'  => $item->added_by_username,
                        'strikeThrough' => $item->pivot->is_received,
                        'showTier'      => true,
                        'tierMode'      => $guild->tier_mode,
                    ])
                    @include('character/partials/itemDetails', ['hideCreatedAt' => true])
                </li>

                @php
                    $lastInstanceId = $item->instance_id;
                @endphp
            @endforeach
        </ol>

        <ol class="js-wishlist-unsorted" style="{{ $guild->do_sort_items_by_instance ? 'display:none;' : '' }}">
            @foreach ($wishlist as $item)
                <li value="{{ $item->pivot->order }}">
                    @include('partials/item', [
                        'wowheadLink'   => false,
                        'itemDate'      => $item->pivot->created_at,
                        'itemUsername'  => $item->added_by_username,
                        'strikeThrough' => $item->pivot->is_received,
                        'showTier'      => true,
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
