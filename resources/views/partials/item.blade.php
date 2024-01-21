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
    $itemFaction = $item->faction;
    if (isset($guild) && $guild->faction) {
        $itemFaction = null;
    }
}

$wowheadSubdomain = 'www';

// Long name to avoid conflicts with whatever view is using this template
$itemExpansionId = null;

if (isset($guild) && $guild->expansion_id) {
    $itemExpansionId = $guild->expansion_id;
} else if (isset($item) && isset($item->expansion_id)) {
    $itemExpansionId = $item->expansion_id;
}

if ($itemExpansionId === 1 || $itemExpansionId === 4) {
    $wowheadSubdomain = 'classic';
} else if ($itemExpansionId === 2) {
    $wowheadSubdomain = 'tbc';
} else if ($itemExpansionId === 3) {
    $wowheadSubdomain = 'wotlk';
} else if ($itemExpansionId === 5) {
    $wowheadSubdomain = 'cataclysm';
}

if (!isset($wowheadLocale)) {
    $wowheadLocale = App::getLocale();
}

$wowheadLocaleNoDot = $wowheadLocale;

if ($wowheadLocale === 'en') {
    $wowheadLocale = '';
    $wowheadLocaleNoDot = '';
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

$wowheadAttribs = 'data-wowhead="item=' . $itemId . '?domain=' . ($wowheadLocaleNoDot ? $wowheadLocale : '') . $wowheadSubdomain. '" ';
$wowheadUrl = null;

if ($itemExpansionId === 3 || $itemExpansionId === 5) {
    $wowheadUrl = 'https://' . $wowheadLocale . 'wowhead.com/' . $wowheadSubdomain . '/' . ($wowheadLocaleNoDot ? $wowheadLocaleNoDot . '/' : null) . 'item=' . $itemId;
    $wowheadAttribs .= 'data-wowhead-link="' . $wowheadUrl . '?domain=' . $wowheadLocale . $wowheadSubdomain . '"';
} else {
    $wowheadUrl = 'https://' . $wowheadLocale . $wowheadSubdomain . '.wowhead.com/item=' . $itemId;
    $wowheadAttribs .= 'data-wowhead-link="' . $wowheadUrl . '?domain=' . $wowheadLocale . $wowheadSubdomain . '"';
}

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
            <a href="{{ $wowheadUrl }}" target="_blank" class="{{ isset($itemQuality) ? $itemQuality : '' }}">{{ $itemName }}</a>
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
            <a href="{{ $wowheadUrl }}" target="_blank" class="{{ isset($itemQuality) ? $itemQuality : '' }}">{{ $itemName }}</a>
        @endif
    </span>
    @if (isset($itemFaction))
        @if ($itemFaction === 'h')
            <span class="text-horde font-weight-bold" title="{{ __('Horde') }}">H</span>
        @elseif ($itemFaction === 'a')
            <span class="text-alliance font-weight-bold" title="{{ __('Alliance') }}">A</span>
        @endif
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
