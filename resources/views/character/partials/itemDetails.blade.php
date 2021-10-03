@if ($item)
    <ul class="no-bullet no-indent small text-muted ml-3">
        <li>
            <ul class="list-inline">
                @if ($item->pivot->is_offspec)
                    <li class="list-inline-item">
                        <span class="font-weight-bold text-white" title="Offspec">{{ __("OS") }}</span>
                    </li>
                @endif
                @if ((!isset($hideCreatedAt) || !$hideCreatedAt) && $item->pivot->created_at))
                    <li class="cursor-pointer js-timestamp-title list-inline-item" data-timestamp="{{ $item->pivot->created_at }}">
                        added <span class="js-watchable-timestamp" data-timestamp="{{ $item->pivot->created_at }}"></span> {{ __("ago") }}
                        @if (isset($item->pivot->type) && $item->pivot->type == App\Item::TYPE_RECEIVED)
                            (backdated)
                        @endif
                    </li>
                @endif
                @if (!isset($hideAddedBy) || !$hideAddedBy)
                    <li class="list-inline-item">
                        {{ __("by") }}
                        <a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $item->pivot->added_by, 'usernameSlug' => slug($item->added_by_username)]) }}" class="text-muted">
                            {{ $item->added_by_username }}
                        </a>
                        @if ((!isset($hideRaidGroup) || !$hideRaidGroup) && $item->raid_group_name)
                            &sdot;
                            {{ $item->raid_group_name }}
                        @endif
                        @if ((!isset($hideRaid) || !$hideRaid) && $item->raid_name)
                            &sdot;
                            <a class="text-muted" href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $item->pivot->raid_id, 'raidSlug' => ($item->raid_slug ? $item->raid_slug : null)]) }}">
                                {{ $item->raid_name }}
                            </a>
                        @endif
                    </li>
                @endif
            </ul>
        </li>
        @if ($item->pivot->note)
            <li>
                <span class="font-weight-medium">Note:</span> {{ $item->pivot->note }}
            </li>
        @endif
        @if (isset($showOfficerNote) && $showOfficerNote && $item->pivot->officer_note)
            <li>
                <span class="font-weight-medium">Officer Note:</span> {{ $item->pivot->officer_note }}
            </li>
        @endif
    </ul>
@endif
