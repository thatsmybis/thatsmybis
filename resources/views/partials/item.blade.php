@php
if (isset($item)) {
    $itemName = $item->name;
    $itemId = $item->item_id;
}
@endphp

<span class="font-weight-{{ isset($fontWeight) && $fontWeight ? $fontWeight : 'medium' }} {{ isset($strikeThrough) && $strikeThrough ? 'font-strikethrough' : '' }}">
    @if (isset($wowheadLink) && $wowheadLink)
        <a href="https://classic.wowhead.com/item={{ $itemId }}" target="_blank">{{ $itemName }}</a>
    @elseif (isset($guild) && $guild)
        @if (isset($auditLink) && $auditLink)
            <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $itemId]) }}"
                target="{{isset($targetBlank) && $targetBlank ? '_blank' : '' }}"
                data-wowhead="item={{ $itemId }}?domain=classic"
                data-wowhead-link="https://classic.wowhead.com/item={{ $itemId }}">
                {{ $itemName }}
            </a>
        @else
            <a href="{{ route('guild.item.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $itemId, 'slug' => slug($itemName)]) }}"
                target="{{isset($targetBlank) && $targetBlank ? '_blank' : '' }}"
                data-wowhead="item={{ $itemId }}?domain=classic"
                data-wowhead-link="https://classic.wowhead.com/item={{ $itemId }}">
                {{ $itemName }}
            </a>
        @endif
    @else
        <a href="{{ route('item.show', ['item_id' => $itemId, 'slug' => slug($itemName)]) }}"
            target="{{isset($targetBlank) && $targetBlank ? '_blank' : '' }}"
            data-wowhead="item={{ $itemId }}?domain=classic"
            data-wowhead-link="https://classic.wowhead.com/item={{ $itemId }}?domain=classic">
            {{ $itemName }}
        </a>
    @endif
</span>

@if (isset($itemDate) && $itemDate)
    <span class="js-watchable-timestamp js-timestamp-title smaller text-muted"
        data-timestamp="{{ $itemDate }}"
        @if (isset($itemUsername) && $itemUsername)
            data-title="added by {{ $itemUsername }} at"
        @endif
        data-is-short="1">
    </span>
@endif
