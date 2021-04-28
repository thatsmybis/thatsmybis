<ul class="no-bullet no-indent">
    <li>
        <ul class="list-inline">
            <li class="list-inline-item">
                <h{{ isset($headerSize) && $headerSize ? $headerSize : '1' }} class="mb-0 font-weight-bold">
                    {{ isset($titlePrefix) && $titlePrefix ? $titlePrefix : '' }}<a href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]) }}" class="text-white">{{ $member->username }}</a>{{ isset($titleSuffix) && $titleSuffix ? $titleSuffix : '' }}
                </h{{ isset($headerSize) && $headerSize ? $headerSize : '1' }}>
            </li>
            @if (isset($showEdit) && $showEdit)
                <li class="list-inline-item">
                    <a href="{{ route('member.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]) }}">
                        <span class="fas fa-fw fa-pencil"></span>
                        edit
                    </a>
                </li>
            @endif
            @if (isset($showLogs) && $showLogs)
                <li class="list-inline-item">
                    <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'member_id' => $member->id]) }}">
                        <span class="fas fa-fw fa-clipboard-list-check"></span>history
                    </a>
                </li>
            @endif
        </ul>
    </li>
    <li>
        <ul class="list-inline">
            @if ($member->inactive_at)
                <li class="list-inline-item">
                    <small>
                        <span class="font-weight-bold text-danger">INACTIVE</span>
                    </small>
                </li>
            @endif
            <li class="list-inline-item">
                Member
                @if (!$guild->is_attendance_hidden && (isset($attendancePercentage) || isset($raidCount)))
                    <span class="small">
                        @include('partials/attendanceTag', ['attendancePercentage' => (isset($attendancePercentage) ? $attendancePercentage : null), 'raidCount' => (isset($raidCount) ? $raidCount : null), 'smallRaid' => false])
                    </span>
                @endif
            </li>
            @if (isset($showEdit) && $showEdit)
                @if ($member->is_received_unlocked)
                    <li class="list-inline-item">
                        <span class="text-warning small">loot unlocked</span>
                    </li>
                @endif
                @if ($member->is_wishlist_unlocked)
                    <li class="list-inline-item">
                        <span class="text-warning small">wishlist unlocked</span>
                    </li>
                @endif
            @endif
        </ul>
    </li>
    <li>
        {{-- Don't let this get lazy loaded on its own; force the dev to do it intentionally to avoid poor performance --}}
        @if (isset($guild) && $guild)
            <span class="text-uncommon">
                &lt;{{ $guild->name }}&gt;
            </span>
        @endif

        @if (isset($discordUsername) && $discordUsername)
            <span class="text-discord">
                <span class="fab fa-fw fa-discord"></span> {{ $discordUsername }}
            </span>
        @endif
    </li>

    {{-- Don't let this get lazy loaded on its own; force the dev to do it intentionally to avoid poor performance --}}
    @if ($member->relationLoaded('roles') && $member->roles->count() > 0)
        <li class="mt-2">
            <ul class="list-inline">
                @foreach ($member->roles as $role)
                    <li class="list-inline-item">
                        <span class="tag" style="border-color:{{ $role->getColor() }};"><span class="role-circle" style="background-color:{{ $role->getColor() }}"></span>{{ $role->name }}</span>
                    </li>
                @endforeach
            </ul>
        </li>
    @endif
</ul>
