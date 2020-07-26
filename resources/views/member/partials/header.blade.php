<ul class="no-bullet no-indent">
    <li>
        <ul class="list-inline">
            <li class="list-inline-item">
                <h{{ isset($headerSize) && $headerSize ? $headerSize : '1' }} class="mb-0 font-weight-bold">
                    {{ isset($titlePrefix) && $titlePrefix ? $titlePrefix : '' }}<a href="{{ route('member.show', ['guildSlug' => $guild->slug, 'username' => $member->username]) }}" class="text-white">{{ $member->username }}</a>{{ isset($titleSuffix) && $titleSuffix ? $titleSuffix : '' }}
                </h{{ isset($headerSize) && $headerSize ? $headerSize : '1' }}>
            </li>
            @if (isset($showEdit) && $showEdit)
                <li class="list-inline-item">
                    <a href="{{ route('member.edit', ['guildSlug' => $guild->slug, 'username' => $member->username]) }}">
                        <span class="fas fa-fw fa-pencil"></span>
                        edit
                    </a>
                </li>
            @endif
        </ul>
    </li>
    <li>
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
    @if (isset($member->roles) && $member->roles->count() > 0)
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
