<table id="itemSlots" class="table table-border table-hover stripe">
    <thead>
        <tr>
            <th>
                {{ __("Slot") }}
            </th>
            <th>
                {{ __("Received") }}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_HEAD]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Head") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_NECK]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Neck") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_SHOULDERS]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Shoulders") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_BACK]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Back") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_CHEST_1, App\Item::SLOT_CHEST_2]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Chest") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_WRIST]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Wrists") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_WAIST]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Waist") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_HANDS]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Hands") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_LEGS]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Legs") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_FEET]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Feet") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_FINGER]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Finger") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_TRINKET]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Trinket") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_WEAPON_MAIN_HAND, App\Item::SLOT_WEAPON_TWO_HAND, App\Item::SLOT_WEAPON_ONE_HAND, App\Item::SLOT_WEAPON_OFF_HAND]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Weapon") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_SHIELD, App\Item::SLOT_OFFHAND]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Offhand") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_RANGED_1, App\Item::SLOT_RANGED_2, App\Item::SLOT_THROWN, App\Item::SLOT_RELIC]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Ranged/Relic") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
        <tr>
            @php
                $slotItems = $items->whereIn('inventory_type', [App\Item::SLOT_MISC, App\Item::SLOT_SHIRT, App\Item::SLOT_BAG, App\Item::SLOT_AMMO]);
            @endphp
            <td class="font-weight-bold">
                {{ __("Misc") }}
                <span class="small text-muted">{{ $slotItems->count() }}</span>
            </td>
            <td>
                @foreach ($slotItems as $item)
                    {{ $item->inventory_type }}
                    @include('partials/item', [
                        'wowheadLink'  => false,
                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                        'itemUsername' => $item->added_by_username,
                        'showTier'     => true,
                        'tierMode'     => $guild->tier_mode,
                    ])
                @endforeach
            </td>
        </tr>
    </tbody>
</table>
