<li class="list-inline-item">
    <div class="dropdown">
        <a class="dropdown-toggle text-{{ $text }}" id="raid{{ $raid->id }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            @if (isset($raidGroup) && $raidGroup)
                <span class="role-circle" style="background-color:{{ $raidGroup->getColor() }}"></span>
            @endif
            <span class="{{ isset($bold) && $bold ? 'font-weight-bold' : '' }} text-5">
            {{ $raid->name }}
            @if ($raid->cancelled_at)
                <span class="font-weight-bold text-warning">
                    {{ __("cancelled") }}
                </span>
            @endif
        </a>
        <div class="dropdown-menu" aria-labelledby="raid{{ $raid->id }}Dropdown">
            <a class="dropdown-item" href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]) }}">
                <span class="text-muted fa-fw fas fa-"></span>
                {{ __("View") }}
            </a>
            @if (!isset($showAssignLoot) || $showAssignLoot)
                <a class="dropdown-item" href="{{ route('item.assignLoot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                    <span class="text-muted fa-fw fas fa-sack"></span>
                    {{ __("Assign Loot") }}
                </a>
            @endif
            @if (!isset($showEdit) || $showEdit)
                <a class="dropdown-item" href="{{ route('guild.raids.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]) }}">
                    <span class="text-muted fa-fw fas fa-pencil"></span>
                    {{ __("Edit") }}
                </a>
                <a class="dropdown-item" href="{{ route('guild.raids.copy', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id]) }}">
                    <span class="text-muted fa-fw fas fa-copy"></span>
                    {{ __("Copy") }}
                </a>
            @endif
            <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_id' => $raid->id]) }}">
                <span class="text-muted fa-fw fas fa-"></span>
                {{ __("History") }}
            </a>
            @if ($raid->archived_at)
                <span class="dropdown-item disabled font-weight-bold text-danger">
                    {{ __("ARCHIVED") }}
                </span>
            @endif
            @if ($raid->cancelled_at)
                <span class="dropdown-item disabled font-weight-bold text-warning">
                    {{ __("CANCELLED") }}
                </span>
            @endif
        </div>
    </div>
</li>
