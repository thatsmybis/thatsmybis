<ul class="no-bullet no-indent">
    <li>
        @include('character/partials/headerTitle')
    </li>
    @if ($character->raid_group_id || $character->class || $character->is_alt)
        <li>
            <ul class="list-inline">
                @if ($character->is_alt)
                    <li class="list-inline-item font-weight-bold">
                        <span class="tag d-inline" style="color: orange;">
                            Alt
                        </span>
                    </li>
                @endif
                {{-- Don't let this get lazy loaded on its own; force the dev to do it intentionally to avoid poor performance --}}
                @if ($character->relationLoaded('raidGroup') && $character->raidGroup)
                    @php
                        $raidGroupColor = null;
                        if ($character->raidGroup->relationLoaded('role')) {
                            $raidGroupColor = $character->raidGroup->getColor();
                        }
                    @endphp
                    <li class="list-inline-item font-weight-bold">
                        <span class="tag d-inline" style="border-color:{{ $raidGroupColor }};"><span class="role-circle" style="background-color:{{ $raidGroupColor }}"></span>
                            {{ $character->raidGroup->name }}
                        </span>
                    </li>
                @elseif ($character->raid_group_name)
                    <li class="list-inline-item font-weight-bold">
                        <span class="tag d-inline" style="border-color:{{ $character->raid_group_color ? getHexColorFromDec($character->raid_group_color) : '' }};"><span class="role-circle" style="background-color:{{ $character->raid_group_color ? getHexColorFromDec($character->raid_group_color) : '' }}"></span>
                            {{ $character->raid_group_name }}
                        </span>
                    </li>
                @endif
                <li class="list-inline-item">
                    {{ $character->class ? $character->class : '' }}
                </li>
            </ul>
        </li>
    @endif

    @if (!isset($showDetails) || $showDetails)
        @if ($character->inactive_at || $character->level || $character->race || $character->spec)
            <li>
                <small>
                    <span class="font-weight-bold text-danger">{{ $character->inactive_at ? 'INACTIVE' : '' }}</span>
                    {{ $character->level ? $character->level : '' }}
                    {{ $character->race  ? $character->race : '' }}
                    {{ $character->spec  ? $character->spec : '' }}
                </small>
            </li>
        @endif

        @if ($character->rank || $character->profession_1 || $character->profession_2 || $character->is_alt)
            <li>
                <small>
                    {{ $character->rank         ? 'Rank ' . $character->rank . ($character->profession_1 || $character->profession_2 ? ',' : '') : '' }}
                    {{ $character->profession_1 ? $character->profession_1 . ($character->profession_2 ? ',' : '') : '' }}
                    {{ $character->profession_2 ? $character->profession_2 : ''}}
                </small>
            </li>
        @endif
    @endif

    @if (!isset($showOwner) || (isset($showOwner) && $showOwner))
        <li>
            <small>
                @if ($character->member_id)
                    {{-- Don't let this get lazy loaded on its own; force the dev to do it intentionally to avoid poor performance --}}
                    @if ($character->relationLoaded('member'))
                        <a href="{{route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $character->member->id, 'usernameSlug' => $character->member->slug]) }}" class="">
                            {{ $character->member->username }}'s character
                        </a>
                    @endif
                @else
                    <span class="font-weight-bold text-danger">
                        Unclaimed
                    </span>
                @endif
            </small>
        </li>
    @endif

    @if (isset($showEdit) && $showEdit && $character->member_id && $character->relationLoaded('member'))
        @if ($character->member->is_received_unlocked)
            <li class="list-inline-item">
                <span class="text-warning small" title="To lock, edit the member that owns this character">loot unlocked</span>
            </li>
        @endif
        @if ($character->member->is_wishlist_unlocked)
            <li class="list-inline-item">
                <span class="text-warning small" title="To lock, edit the member that owns this character">wishlist unlocked</span>
            </li>
        @endif
    @endif
</ul>
