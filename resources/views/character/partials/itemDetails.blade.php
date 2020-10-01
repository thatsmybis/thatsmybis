@if ($item)
    <ul class="no-bullet no-indent small text-muted">
        @if ($item->pivot->is_offspec)
            <li>
                Offspec
            </li>
        @endif
        @if ($item->pivot->note)
            <li>
                <strong>Note:</strong> {{ $item->pivot->note }}
            </li>
        @endif
        @if (isset($showOfficerNote) && $showOfficerNote && $item->pivot->officer_note)
            <li>
                <strong>Officer Note:</strong> {{ $item->pivot->officer_note }}
            </li>
        @endif
        @if ((!isset($hideRaid) || !$hideRaid) && $item->raid_name)
            <li>
                $item->raid_name
            </li>
        @endif
        @if ((!isset($hideCreatedAt) || !$hideCreatedAt) && $item->pivot->created_at)
            <li class="cursor-pointer js-timestamp-title" data-timestamp="{{ $item->pivot->created_at }}">
                added <span class="js-watchable-timestamp" data-timestamp="{{ $item->pivot->created_at }}"></span> ago
            </li>
        @endif
        @if ($item->pivot->received_at)
            <li class="cursor-pointer js-timestamp-title" data-timestamp="{{ $item->pivot->received_at }}">
                received <span class="js-watchable-timestamp" data-timestamp="{{ $item->pivot->received_at }}"></span> ago
                @if (isset($item->pivot->type) && $item->pivot->type == App\Item::TYPE_RECEIVED)
                    (backdated)
                @endif
            </li>
        @endif
        <li>
            by <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $item->pivot->added_by, 'usernameSlug' => slug($item->added_by_username)]) }}" class="text-muted" target="_blank">
                    {{ $item->added_by_username }}
                </a>
        </li>
    </ul>
@endif
