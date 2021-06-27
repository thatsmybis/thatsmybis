@foreach ($characters as $character)
    @php
        $characterNameText = $character->name;
        $characterMetaText = (isset($showClass) && $showClass ? ($character->class ? '(' . $character->class . ')&nbsp;' : '') : '');
        $characterMetaText .= ($character->is_alt ? "Alt" : '');
        if (isset($raidGroups) && $raidGroups->where('id', $character->raid_group_id)->count()) {
            $characterMetaText .= '&sdot; ' . ($raidGroups->where('id', $character->raid_group_id)->first()->name);
        }
    @endphp
    <option value="{{ $character->id }}"
        data-tokens="{{ $character->id }}"
        data-raid-group-id="{{ $character->raid_group_id }}"
        data-name="{{ $character->name }}"
        data-class="text-{{ strtolower($character->class) }}"
        class="js-character-option text-{{ strtolower($character->class) }}-important"
        data-content="<span class='font-weight-medium text-{{ strtolower($character->class) }}-important'>{{ $characterNameText }}</span> <span class='small'>{{ $characterMetaText }}</span>"
        hack="{{ $character->id }}">
        {{ $characterNameText }} {{ $characterMetaText }}
    </option>
@endforeach
