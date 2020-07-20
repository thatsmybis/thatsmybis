<span class="font-weight-medium">
    @if (isset($wowheadLink) && $wowheadLink)
        <a href="https://classic.wowhead.com/item={{ $item->item_id }}" target="_blank">{{ $item->name }}</a>
    @elseif (isset($guild) && $guild)
        <a href="{{ route('guild.item.show', ['guildSlug' => $guild->slug, 'item_id' => $item->item_id, 'slug' => slug($item->name)]) }}"
            data-wowhead="item={{ $item->item_id }}?domain=classic"
            data-wowhead-link="https://classic.wowhead.com/item={{ $item->item_id }}">
            {{ $item->name }}
        </a>
    @else
        <a href="{{ route('item.show', ['item_id' => $item->item_id, 'slug' => slug($item->name)]) }}"
            data-wowhead="item={{ $item->item_id }}?domain=classic"
            data-wowhead-link="https://classic.wowhead.com/item={{ $item->item_id }}?domain=classic">
            {{ $item->name }}
        </a>
    @endif
</span>
