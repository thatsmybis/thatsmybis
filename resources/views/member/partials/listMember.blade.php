<div class="dropdown">
    <a class="dropdown-toggle font-weight-bold text-white" id="member{{ $member->id }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ $member->username }}
    </a>
    <div class="dropdown-menu" aria-labelledby="member{{ $member->id }}Dropdown">
        <a class="dropdown-item" href="{{ route('member.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]) }}" target="_blank">
            Profile
        </a>
        <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'member_id' => $member->id]) }}" target="_blank">
            Logs
        </a>
        <a class="dropdown-item" href="{{ route('member.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'memberId' => $member->id, 'usernameSlug' => $member->slug]) }}" target="_blank">
            Edit
        </a>
    </div>
</div>
<span class="text-discord">
    {{ $member->user->discord_username }}
</span>
