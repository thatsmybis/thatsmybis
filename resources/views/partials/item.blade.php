@php
if (isset($item)) {
    $itemName = $item->name;
    $itemId   = $item->item_id;
    if (isset($item->quality)) {
        $itemQuality = 'q' . $item->quality;
    }
}

$wowheadSubdomain = 'www';

if (isset($guild) && $guild->expansion_id) {
    if ($guild->expansion_id === 1) {
        $wowheadSubdomain = 'classic';
    } else if ($guild->expansion_id === 2) {
        $wowheadSubdomain = 'tbc';
    }
} else if (isset($item) && isset($item->expansion_id)) {
    if ($item->expansion_id === 1) {
    $wowheadSubdomain = 'classic';
    } else if ($item->expansion_id === 2) {
        $wowheadSubdomain = 'tbc';
    }
}

$wowheadAttribs = 'data-wowhead="item=' . $itemId . '?domain=' . $wowheadSubdomain . '" data-wowhead-link="https://' . $wowheadSubdomain . '.wowhead.com/item=' . $itemId . '?domain=' . $wowheadSubdomain . '"';
@endphp

<span class="font-weight-{{ isset($fontWeight) && $fontWeight ? $fontWeight : 'medium' }} {{ isset($strikeThrough) && $strikeThrough ? 'font-strikethrough' : '' }}">
    @if (isset($wowheadLink) && $wowheadLink)
        <a href="https://{{ $wowheadSubdomain }}wowhead.com/item={{ $itemId }}" target="_blank" class="{{ isset($itemQuality) ? $itemQuality : '' }}">{{ $itemName }}</a>
    @elseif (isset($guild) && $guild)
        @if (isset($auditLink) && $auditLink)
            <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $itemId]) }}"
                target="{{isset($targetBlank) && $targetBlank ? '_blank' : '' }}"
                {!! $wowheadAttribs !!}
                class="{{ isset($itemQuality) ? $itemQuality : '' }}">
                {{ $itemName }}
            </a>
        @else
            <a href="{{ route('guild.item.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $itemId, 'slug' => slug($itemName)]) }}"
                target="{{isset($targetBlank) && $targetBlank ? '_blank' : '' }}"
                {!! $wowheadAttribs !!}
                class="{{ isset($itemQuality) ? $itemQuality : '' }}">
                {{ $itemName }}
            </a>
        @endif
    @else
        <a href="{{ route('item.show', ['item_id' => $itemId, 'slug' => slug($itemName)]) }}"
            target="{{isset($targetBlank) && $targetBlank ? '_blank' : '' }}"
            {!! $wowheadAttribs !!}
            class="{{ isset($itemQuality) ? $itemQuality : '' }}">
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
