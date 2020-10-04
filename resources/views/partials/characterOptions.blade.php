@foreach ($characters as $character)
    <option value="{{ $character->id }}"
        data-tokens="{{ $character->id }}"
        data-raid-id="{{ $character->raid_id }}"
        data-name="{{ $character->name }}"
        class="js-character-option text-{{ strtolower($character->class) }}-important"
        hack="{{ $character->id }}">
        {{ $character->name }} &nbsp; {{ $character->class ? '(' . $character->class . ')' : '' }} &nbsp; {{ $character->is_alt ? "Alt" : '' }}
    </option>
@endforeach
