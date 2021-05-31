@php
    $raidGroup = ($character->raid_group_id ? $guild->raidGroups->where('id', $character->raid_group_id)->first() : null);
@endphp
<li class="list-inline-item text-{{ $character->inactive_at ? 'muted' : strtolower($character->class) }}">
    <div class="dropdown">
        <a class="dropdown-toggle text-{{ $character->inactive_at ? 'muted' : strtolower($character->class) }}" id="character{{ $character->id }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="role-circle" style="background-color:{{ $raidGroup ? $raidGroup->getColor() : null }}"></span>
            <span class="{{ isset($bold) && $bold ? 'font-weight-bold' : '' }}">
            {{ $character->name }}
            @if ($character->is_alt)
                <span class="small text-muted">
                    alt
                </span>
            @endif
            @if (!$guild->is_attendance_hidden)
                <span class="small">
                    @include('partials/attendanceTag', ['attendancePercentage' => $character->attendance_percentage, 'raidCount' => $character->raid_count, 'smallRaid' => false])
                </span>
            @endif
        </a>
        <div class="dropdown-menu" aria-labelledby="character{{ $character->id }}Dropdown">
            <a class="dropdown-item" href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                <span class="text-muted fa-fw fas fa-"></span>
                Profile
            </a>
            @if (!isset($showLogs) || $showLogs)
                <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'character_id' => $character->id]) }}">
                    <span class="text-muted fa-fw fas fa-"></span>
                    History
                </a>
            @endif
            @if (!isset($showEditLoot) || $showEditLoot)
                <a class="dropdown-item" href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                    <span class="text-muted fa-fw fas fa-sack"></span>
                    Loot
                </a>
            @endif
            @if (!isset($showEdit) || $showEdit)
                <a class="dropdown-item" href="{{ route('character.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                    <span class="text-muted fa-fw fas fa-pencil"></span>
                    Edit
                </a>
            @endif
            <span class="dropdown-item disabled">
                @if ($raidGroup)
                    @include('partials/raidGroup', ['raidGroupColor' => $raidGroup->getColor()])
                @else
                    no raid groups
                @endif
            </span>
            @if ($character->inactive_at)
                <span class="dropdown-item disabled font-weight-bold text-danger">
                    ARCHIVED
                </span>
            @endif
        </div>
    </div>
</li>
