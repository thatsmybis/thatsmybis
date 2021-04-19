<li class="list-inline-item">
    <div class="dropdown">
        <a class="dropdown-toggle text-{{ $text }}" id="raid{{ $raid->id }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="role-circle" style="background-color:{{ $raidGroup ? $raidGroup->getColor() : null }}"></span>
            <span class="{{ isset($bold) && $bold ? 'font-weight-bold' : '' }}">
            {{ $raid->name }}
            @if ($raid->cancelled_at)
                <span class="small text-warning">
                    cancelled
                </span>
            @endif
        </a>
        <div class="dropdown-menu" aria-labelledby="raid{{ $raid->id }}Dropdown">
            <a class="dropdown-item" href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]) }}">
                View
            </a>
            @if (!isset($showEdit) || $showEdit)
                <a class="dropdown-item" href="{{ route('guild.raids.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]) }}">
                    Edit
                </a>
            @endif
            <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                Logs
            </a>
            @if ($raid->cancelled_at)
                <span class="dropdown-item disabled font-weight-bold text-warning">
                    CANCELLED
                </span>
            @endif
        </div>
    </div>
</li>
