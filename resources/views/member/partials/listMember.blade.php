<ul class="no-indent no-bullet">
    <li>
        <div class="dropdown">
            @if ($member->user_id == $guild->user_id)
                <span class="fas fa-fw fa-crown text-gold" title="guild owner on this website"></span>
            @endif
            <a class="dropdown-toggle font-weight-bold text-white" id="member{{ $member->id }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ $member->username }}
            </a>
            <div class="dropdown-menu" aria-labelledby="member{{ $member->id }}Dropdown">
                <a class="dropdown-item" href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]) }}">
                    <span class="text-muted fa-fw fas fa-"></span>
                    Profile
                </a>
                <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'member_id' => $member->id]) }}">
                    <span class="text-muted fa-fw fas fa-"></span>
                    History
                </a>
                <a class="dropdown-item" href="{{ route('member.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]) }}">
                    <span class="text-muted fa-fw fas fa-pencil"></span>
                    Edit
                </a>
            </div>
        </div>
    </li>
    <li>
        <span class="text-discord">
            {{ $member->user->discord_username }}
        </span>
    </li>
    @if (!$guild->is_attendance_hidden && (isset($raidCount) || isset($attendancePercentage)))
        <li>
            @include('partials/attendanceTag', ['attendancePercentage' => (isset($attendancePercentage) ? $attendancePercentage : null), 'raidCount' => (isset($raidCount) ? $raidCount : null), 'smallRaid' => false])
        </li>
    @endif
    @if ($member->is_received_unlocked)
        <li>
            <span class="font-weight-bold text-warning small">loot unlocked</span>
        </li>
    @endif
    @if ($member->is_wishlist_unlocked)
        <li>
            <span class="font-weight-bold text-warning small">wishlist unlocked</span>
        </li>
    @endif
</ul>
