@php
if (isset($item)) {
    $itemName = $item->name;
    $itemId = $item->item_id;
}
@endphp

<span class="font-weight-{{ isset($fontWeight) && $fontWeight ? $fontWeight : 'medium' }}">
    @if (isset($wowheadLink) && $wowheadLink)
        <a href="https://classic.wowhead.com/item={{ $itemId }}" target="_blank">{{ $itemName }}</a>
    @elseif (isset($guild) && $guild)
        <a href="{{ route('guild.item.show', ['guildSlug' => $guild->slug, 'item_id' => $itemId, 'slug' => slug($itemName)]) }}"
            data-wowhead="item={{ $itemId }}?domain=classic"
            data-wowhead-link="https://classic.wowhead.com/item={{ $itemId }}">
            {{ $itemName }}
        </a>
    @else
        <a href="{{ route('item.show', ['item_id' => $itemId, 'slug' => slug($itemName)]) }}"
            data-wowhead="item={{ $itemId }}?domain=classic"
            data-wowhead-link="https://classic.wowhead.com/item={{ $itemId }}?domain=classic">
            {{ $itemName }}
        </a>
    @endif
</span>
