@php
$itemName    = '';
$itemId      = '';
$itemQuality = null;
if (isset($item)) {
    $itemName = $item->name;
    $itemId   = $item->item_id;
    if (isset($item->quality)) {
        $itemQuality = 'q' . $item->quality;
    }
}

// TODO: Only Classic has valid links as of 2021-02-16. Update this when other expansions are supported.
$wowheadSubdomain = 'www';
if (isset($guild) && $guild->expansion_id === 1 || (isset($item) && isset($item->expansion_id) && $item->expansion_id === 1)) {
    $wowheadSubdomain = 'classic';
}

$wowheadAttribs = 'data-wowhead="item=' . $itemId . '?domain=' . $wowheadSubdomain . '" data-wowhead-link="https://' . $wowheadSubdomain . '.wowhead.com/item=' . $itemId . '?domain=' . $wowheadSubdomain . '"';
@endphp

<span class="font-weight-{{ isset($fontWeight) && $fontWeight ? $fontWeight : 'medium' }} {{ isset($strikeThrough) && $strikeThrough ? 'font-strikethrough' : '' }}">
    @if (isset($wowheadLink) && $wowheadLink)
        <a href="https://{{ $wowheadSubdomain }}wowhead.com/item={{ $itemId }}" target="_blank" class="{{ $itemQuality }}">{{ $itemName }}</a>
    @elseif (isset($guild) && $guild)
        @if (isset($auditLink) && $auditLink)
            <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $itemId]) }}"
                target="{{isset($targetBlank) && $targetBlank ? '_blank' : '' }}"
                {!! $wowheadAttribs !!}
                class="{{ $itemQuality }}">
                {{ $itemName }}
            </a>
        @else
            <a href="{{ route('guild.item.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'item_id' => $itemId, 'slug' => slug($itemName)]) }}"
                target="{{isset($targetBlank) && $targetBlank ? '_blank' : '' }}"
                {!! $wowheadAttribs !!}
                class="{{ $itemQuality }}">
                {{ $itemName }}
            </a>
        @endif
    @else
        <a href="{{ route('item.show', ['item_id' => $itemId, 'slug' => slug($itemName)]) }}"
            target="{{isset($targetBlank) && $targetBlank ? '_blank' : '' }}"
            {!! $wowheadAttribs !!}
            class="{{ $itemQuality }}">
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
