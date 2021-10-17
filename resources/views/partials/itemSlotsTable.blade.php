@php
    $slots = [
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_HEAD]),
            'name' => __("Head"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_NECK]),
            'name' => __("Neck"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_SHOULDERS]),
            'name' => __("Shoulders"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_BACK]),
            'name' => __("Back"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_CHEST_1, App\Item::SLOT_CHEST_2]),
            'name' => __("Chest"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_WRIST]),
            'name' => __("Wrists"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_WAIST]),
            'name' => __("Waist"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_HANDS]),
            'name' => __("Hands"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_LEGS]),
            'name' => __("Legs"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_FEET]),
            'name' => __("Feet"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_FINGER]),
            'name' => __("Finger"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_TRINKET]),
            'name' => __("Trinket"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_WEAPON_MAIN_HAND, App\Item::SLOT_WEAPON_TWO_HAND, App\Item::SLOT_WEAPON_ONE_HAND, App\Item::SLOT_WEAPON_OFF_HAND]),
            'name' => __("Weapon"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_SHIELD, App\Item::SLOT_OFFHAND]),
            'name' => __("Offhand"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_RANGED_1, App\Item::SLOT_RANGED_2, App\Item::SLOT_THROWN, App\Item::SLOT_RELIC]),
            'name' => __("Ranged/Relic"),
        ],
        [
            'items' => $items->whereIn('inventory_type', [App\Item::SLOT_MISC, App\Item::SLOT_SHIRT, App\Item::SLOT_BAG, App\Item::SLOT_AMMO]),
            'name' => __("Misc"),
        ],
    ];
@endphp
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
        @foreach ($slots as $slot)
            <tr>
                <td class="font-weight-bold">
                    {{ $slot['name'] }}
                    <span class="small text-muted">{{ count($slot['items']) }}</span>
                </td>
                <td>
                    @if (count($slot['items']))
                        <ul class="list-inline">
                            @foreach ($slot['items'] as $item)
                                <li class="list-inline-item">
                                    @include('partials/item', [
                                        'wowheadLink'  => false,
                                        'fontWeight'   => 'normal',
                                        'itemDate'     => ($item ? ($item->pivot->received_at ? $item->pivot->received_at : $item->pivot->created_at) : null),
                                        'itemUsername' => $item->added_by_username,
                                        'showTier'     => true,
                                        'tierMode'     => $guild->tier_mode,
                                    ])
                                </li>
                            @endforeach
                        </ul>
                    @else
                        —
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
