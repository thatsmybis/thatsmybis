<span class="font-weight-medium">
    @if (isset($wowheadLink) && $wowheadLink)
        <a href="https://classic.wowhead.com/item={{ $item->item_id }}" target="_blank">{{ $item->name }}</a>
    @else
        <a href="{{ route('showItem', ['item_id' => $item->item_id]) }}" data-wowhead="item={{ $item->item_id }}">{{ $item->name }}</a>
    @endif
</span>
