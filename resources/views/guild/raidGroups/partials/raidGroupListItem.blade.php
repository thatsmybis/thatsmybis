<ul class="list-inline">
    <li class="list-inline-item">
        <span class="role-circle" style="{{ $raidGroup->role ? 'background-color:' . $raidGroup->role->getColor() : '' }}" title="{{ $raidGroup->role ? $raidGroup->role->getColor() : ''}}"></span>
        <span class="font-weight-bold text-danger">{{ $raidGroup->disabled_at ? __('ARCHIVED') : '' }}</span>
        <span class="text-5 font-weight-medium" title="{{ $raidGroup->slug }}">{{ $raidGroup->name }}</span>
    </li>
    @if ($raidGroup->role)
        <li class="list-inline-item text-muted">
            &sdot;
        </li>
        <li class="list-inline-item text-muted">
            <span title="Discord Role: {{ $raidGroup->role->discord_id }}">{{ $raidGroup->role->name }}</span>
        </li>
    @endif
    <li class="list-inline-item text-muted">
        &sdot;
    </li>
    <li class="list-inline-item">
        <div class="dropdown">
            <a class="dropdown-toggle text-gold" id="raidGroup{{ $raidGroup->id }}AttendanceDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="fas fa-fw fa-user-check"></span>
                {{ __("attendance") }}
            </a>
            <div class="dropdown-menu" aria-labelledby="raidGroup{{ $raidGroup->id }}AttendanceDropdown">
                <a class="dropdown-item" href="{{ route('guild.raidGroup.attendance', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id, 'type' => 'all']) }}" class="">
                    {{ __("all raiders") }}
                </a>
                <a class="dropdown-item" href="{{ route('guild.raidGroup.attendance', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}" class="">
                    {{ __("main raider") }}
                </a>
                <a class="dropdown-item" href="{{ route('guild.raidGroup.attendance', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id, 'type' => 'secondary']) }}" class="">
                    {{ __("general raider") }}
                </a>
            </div>
        </div>
    </li>
    <li class="list-inline-item text-muted">
        &sdot;
    </li>
    <li class="list-inline-item text-muted">
        @if (!isset($showEdit) || $showEdit)
            <a href="{{ route('guild.raidGroup.mainCharacters', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}">
                {{ trans_choice(":count main raider|:count main raiders", $raidGroup->characters_count) }}
            </a>
        @else
            {{ trans_choice(":count main raider|:count main raiders", $raidGroup->characters_count) }}
        @endif
    </li>
    <li class="list-inline-item text-muted">
        &sdot;
    </li>
    <li class="list-inline-item text-muted">
        @if (!isset($showEdit) || $showEdit)
            <a href="{{ route('guild.raidGroup.secondaryCharacters', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}">
                {{ trans_choice(":count general raider|:count general raiders", $raidGroup->secondary_characters_count) }}
            </a>
        @else
            {{ trans_choice(":count general raider|:count general raiders", $raidGroup->secondary_characters_count) }}
        @endif
    </li>
</ul>
<ul class="list-inline">
    @if (!isset($showEdit) || $showEdit)
        <li class="list-inline-item">
            <a href="{{ route('guild.raidGroup.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'id' => $raidGroup->id]) }}">
                <span class="fas fa-fw fa-pencil"></span>{{ __("edit") }}
            </a>
        </li>
    @endif
    <li class="list-inline-item">
        <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raid_group_id' => $raidGroup->id]) }}">
            <span class="fas fa-fw fa-clipboard-list-check"></span>{{ __("history") }}
        </a>
    </li>
    <li class="list-inline-item">
        <div class="dropdown">
            <a class="dropdown-toggle" id="raidGroup{{ $raidGroup->id }}ExportDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ __("Export") }}
            </a>
            <div class="dropdown-menu" aria-labelledby="raidGroup{{ $raidGroup->id }}ExportDropdown">
                <a class="dropdown-item" href="{{ route('guild.export.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'csv', 'raidGroupId' => $raidGroup->id]) }}" target="_blank" class="tag">
                    <span class="fas fa-fw fa-file-download text-muted"></span>
                    {{ __("Download CSV") }}
                </a>
                <a class="dropdown-item" href="{{ route('guild.export.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'fileType' => 'html', 'raidGroupId' => $raidGroup->id]) }}" target="_blank" class="tag">
                    <span class="fas fa-fw fa-file-alt text-muted"></span>
                    {{ __("View CSV") }}
                </a>
            </div>
        </div>
    </li>
    @if (!isset($showEdit) || $showEdit)
        <li class="list-inline-item float-right">
            <form class="form-inline" role="form" method="POST" action="{{ route('guild.raidGroup.toggleDisable', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}
                <input hidden name="id" value="{{ $raidGroup->id }}">
                <input hidden type="checkbox" name="disabled_at" value="1" class="" autocomplete="off"
                    {{ $raidGroup->disabled_at ? '' : 'checked' }}>
                <button type="submit"
                    class="btn btn-link text-{{ $raidGroup->disabled_at ? 'success' : 'danger' }} p-0 m-0"
                    title="{{ $raidGroup->disabled_at ? 'Raid Group is shown in dropdowns again' : 'Raid Group is no longer shown in dropdowns. Characters already assigned to this raid group will remain assigned to it.' }}">
                    <span class="fas fa-fw fa-{{ $raidGroup->disabled_at ? 'trash-undo' : 'trash' }}"></span>{{ $raidGroup->disabled_at ? __('unarchive') : __('archive') }}
                </button>
            </form>
        </li>
    @endif
</ul>
