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
            <a class="dropdown-item" href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                <span class="fas fa-fw fa-user"></span> {{ __("Profile") }}
            </a>
            @if (isset($showLogs) && $showLogs)
                <a class="dropdown-item" href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'character_id' => $character->id]) }}">
                    <span class="fas fa-fw fa-clipboard-list-check"></span> {{ __("History") }}
                </a>
            @endif
            @if (isset($showEdit) && $showEdit)
                <a class="dropdown-item" href="{{ route('character.edit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                    <span class="fas fa-fw fa-pencil"></span> {{ __("Edit") }}
                </a>
            @endif
            @if (isset($showEditLoot) && $showEditLoot)
                <a class="dropdown-item" href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                    <span class="fas fa-fw fa-sack"></span> {{ __("Wishlist & Loot") }}
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
                    <span class="fas fa-fw fa-pencil"></span>{{ __("edit") }}
                </a>
            </li>
        @endif
        @if (isset($showEditLoot) && $showEditLoot)
            <li class="list-inline-item">
                <a href="{{ route('character.loot', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->slug]) }}">
                    <span class="fas fa-fw fa-sack"></span>{{ __("wishlist & loot") }}
                </a>
            </li>
        @endif
        @if (isset($showLogs) && $showLogs)
            <li class="list-inline-item">
                <a href="{{ route('guild.auditLog', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'character_id' => $character->id]) }}">
                    <span class="fas fa-fw fa-clipboard-list-check"></span>{{ __("history") }}
                </a>
            </li>
        @endif
    </ul>
@endif
