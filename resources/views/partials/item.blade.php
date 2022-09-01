@php
if (isset($item)) {
    $itemName = $item->name;
    $itemId   = $item->item_id;
    if (isset($item->quality)) {
        $itemQuality = 'q' . $item->quality;
    }
    if (isset($item->guild_tier)) {
        $itemTier = $item->guild_tier;
    }
}

$wowheadSubdomain = 'www';

if (isset($guild) && $guild->expansion_id) {
    if ($guild->expansion_id === 1) {
        $wowheadSubdomain = 'classic';
    } else if ($guild->expansion_id === 2) {
        $wowheadSubdomain = 'tbc';
    } else if ($guild->expansion_id === 3) {
        $wowheadSubdomain = 'wotlk';
    }
} else if (isset($item) && isset($item->expansion_id)) {
    if ($item->expansion_id === 1) {
        $wowheadSubdomain = 'classic';
    } else if ($item->expansion_id === 2) {
        $wowheadSubdomain = 'tbc';
    } else if ($item->expansion_id === 3) {
        $wowheadSubdomain = 'wotlk';
    }
}

if (!isset($wowheadLocale)) {
    $wowheadLocale = App::getLocale();
}

if ($wowheadLocale === 'en') {
    $wowheadLocale = '';
} else {
    $wowheadLocale .= '.';
}

if (isset($showTier) && $showTier) {
    if (isset($tierMode) && $tierMode) {
        $itemTierText = '&nbsp;';
        if (isset($itemTier) && $itemTier) {
            if ($tierMode == \App\Guild::TIER_MODE_S) {
                $itemTierText = \App\Guild::tiers()[$itemTier];
            } else {
                $itemTierText = $itemTier;
            }
        }
    } else {
        $showTier = false;
    }
}

$wowheadAttribs =
      'data-wowhead="item=' . $itemId . '?domain=' . $wowheadLocale . $wowheadSubdomain. '" '
    . 'data-wowhead-link="https://' . $wowheadLocale . $wowheadSubdomain . '.wowhead.com/item=' . $itemId . '?domain=' . $wowheadLocale . $wowheadSubdomain . '"';

// Options: tiny, small, medium, large
if (isset($iconSize) && $iconSize) {
    $wowheadAttribs .= ' data-wh-icon-size="' . $iconSize . '"';
}
@endphp

<span class="font-weight-{{ isset($fontWeight) && $fontWeight ? $fontWeight : 'medium' }}">
    @if (isset($showTier) && $showTier)
        <span class="text-monospace font-weight-medium text-tier-{{ isset($itemTier) && $itemTier ? $itemTier : '' }}">{!! $itemTierText !!}</span>
    @endif
    <span class="{{ isset($strikeThrough) && $strikeThrough ? 'font-strikethrough' : '' }}">
        @if (isset($wowheadLink) && $wowheadLink)
            <a href="https://{{ $wowheadLocale . $wowheadSubdomain }}.wowhead.com/item={{ $itemId }}" target="_blank" class="{{ isset($itemQuality) ? $itemQuality : '' }}">{{ $itemName }}</a>
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
        @elseif (isset($displayOnly) && $displayOnly)
            <a {!! $wowheadAttribs !!} class="{{ isset($itemQuality) ? $itemQuality : '' }}">{{ $itemName }}</a>
        @else
            <a href="https://{{ $wowheadLocale . $wowheadSubdomain }}.wowhead.com/item={{ $itemId }}" target="_blank" class="{{ isset($itemQuality) ? $itemQuality : '' }}">{{ $itemName }}</a>
        @endif
    </span>
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
