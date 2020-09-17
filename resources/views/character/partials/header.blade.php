<ul class="no-bullet no-indent">
    @if (isset($character->pivot->created_at))
        <li>
            <ul class="list-inline">
                <li class="list-inline-item text-muted small">
                    Received <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $character->pivot->created_at }}"></span> ago
                </li>
            </ul>
        </li>
    @endif
    <li>
        @if (isset($useDropdown) && $useDropdown)
            <div class="dropdown">
                <a class="dropdown-toggle" id="character{{ $character->id }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @if (isset($showIcon) && $showIcon && $character->class)
                            <img src="{{ asset('images/' . $character->class . '.jpg') }}" class="class-icon" />
                    @endif
                        <span class="font-weight-bold h{{ isset($headerSize) && $headerSize ? $headerSize : '2' }}">
                            {{ isset($titlePrefix) && $titlePrefix ? $titlePrefix : '' }}<span class="text-{{ $character->class ? strtolower($character->class) : '' }}">{{ $character->name }}</span>{{ isset($titleSuffix) && $titleSuffix ? $titleSuffix : '' }}
                        </span>
                </a>
                <div class="dropdown-menu" aria-labelledby="character{{ $character->id }}Dropdown">
                    <a class="dropdown-item" href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}" target="_blank">
                        Profile
                    </a>
                    @if (isset($showLogs) && $showLogs)
                        <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'character_id' => $character->id]) }}" target="_blank">
                            Logs
                        </a>
                    @endif
                    @if (isset($showEdit) && $showEdit)
                        <a class="dropdown-item" href="{{ route('character.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}" target="_blank">
                            Edit
                        </a>
                    @endif
                    @if (isset($showEditLoot) && $showEditLoot)
                        <a class="dropdown-item" href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}" target="_blank">
                            Loot
                        </a>
                    @endif
                </div>
            </div>
        @else
            <ul class="list-inline">
                @if (isset($showIcon) && $showIcon && $character->class)
                    <li class="list-inline-item">
                        <img src="{{ asset('images/' . $character->class . '.jpg') }}" class="class-icon" />
                    </li>
                @endif
                <li class="list-inline-item">
                    <span class="font-weight-bold h{{ isset($headerSize) && $headerSize ? $headerSize : '2' }}">
                        {{ isset($titlePrefix) && $titlePrefix ? $titlePrefix : '' }}<a href="{{route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}" class="text-{{ $character->class ? strtolower($character->class) : '' }}">{{ $character->name }}</a>{{ isset($titleSuffix) && $titleSuffix ? $titleSuffix : '' }}
                    </span>
                </li>
                @if (isset($showEdit) && $showEdit)
                    <li class="list-inline-item">
                        <a href="{{ route('character.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                            <span class="fas fa-fw fa-pencil"></span>edit
                        </a>
                    </li>
                @endif
                @if (isset($showEditLoot) && $showEditLoot)
                    <li class="list-inline-item">
                        <a href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                            <span class="fas fa-fw fa-sack"></span>loot
                        </a>
                    </li>
                @endif
                @if (isset($showLogs) && $showLogs)
                    <li class="list-inline-item">
                        <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'character_id' => $character->id]) }}">
                            <span class="fas fa-fw fa-clipboard-list-check"></span>logs
                        </a>
                    </li>
                @endif
            </ul>
        @endif
    </li>
    @if ($character->raid_id || $character->class || $character->is_alt)
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
                @if ($character->relationLoaded('raid') && $character->raid)
                    @php
                        $raidColor = null;
                        if ($character->raid->relationLoaded('role')) {
                            $raidColor = $character->raid->getColor();
                        }
                    @endphp
                    <li class="list-inline-item font-weight-bold">
                        <span class="tag d-inline" style="border-color:{{ $raidColor }};"><span class="role-circle" style="background-color:{{ $raidColor }}"></span>
                            {{ $character->raid->name }}
                        </span>
                    </li>
                @elseif ($character->raid_name)
                    <li class="list-inline-item font-weight-bold">
                        <span class="tag d-inline" style="border-color:{{ $character->raid_color ? getHexColorFromDec($character->raid_color) : '' }};"><span class="role-circle" style="background-color:{{ $character->raid_color ? getHexColorFromDec($character->raid_color) : '' }}"></span>
                            {{ $character->raid_name }}
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
                    Unclaimed
                @endif
            </small>
        </li>
    @endif
</ul>
