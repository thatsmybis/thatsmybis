<ul class="no-bullet no-indent">
    <li>
        <ul class="list-inline">
            <li class="list-inline-item">
                <h2 title="ID {{ $character->id }}"
                    class="font-weight-bold">
                    {{ isset($titlePrefix) && $titlePrefix ? $titlePrefix : '' }}<span class="text-{{ $character->class ? strtolower($character->class) : '' }}"><a href="{{route('character.show', ['guildSlug' => $guild->slug, 'id' => $character->name]) }}">{{ $character->name }}</a></span>{{ isset($titleSuffix) && $titleSuffix ? $titleSuffix : '' }}
                </h2>
            </li>
            @if (isset($showEdit) && $showEdit)
                <li class="list-inline-item">
                    <a href="{{ route('character.edit', ['guildSlug' => $guild->slug, 'id' => $character->name]) }}">
                        <span class="fas fa-fw fa-pencil"></span>
                        edit
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @if ($character->raid || $character->class)
        <li>
            <span class="font-weight-bold">
                {{ $character->raid ? $character->raid->name : '' }}
            </span>
            {{ $character->class ? $character->class : '' }}
        </li>
    @endif

    @if ($character->level || $character->race || $character->spec)
        <li>
            <small>
                {{ $character->level ? $character->level : '' }}
                {{ $character->race  ? $character->race : '' }}
                {{ $character->spec  ? $character->spec : '' }}
            </small>
        </li>
    @endif

    @if ($character->rank || $character->profession_1 || $character->profession_2)
        <li>
            <small>
                {{ $character->rank         ? 'Rank ' . $character->rank . ($character->profession_1 || $character->profession_2 ? ',' : '') : '' }}
                {{ $character->profession_1 ? $character->profession_1 . ($character->profession_2 ? ',' : '') : '' }}
                {{ $character->profession_2 ? $character->profession_2 : ''}}
            </small>
        </li>
    @endif

    @if ($character->member)
        <li>
            <small>
                <a href="" class="">
                    {{ $character->member->username }}'s character
                </a>
            </small>
        </li>
    @endif
</ul>
