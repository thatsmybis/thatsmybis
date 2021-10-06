@foreach ($characters as $character)
    @php
        $characterNameText = $character->name;
        $characterMetaText = (isset($showClass) && $showClass ? ($character->class ? '(' . $character->class . ')&nbsp;' : '') : '');
        $characterMetaText .= ($character->is_alt ? "Alt" : '');

        if (isset($raidGroups) && $raidGroups->where('id', $character->raid_group_id)->count()) {
            $characterMetaText .= '&sdot; ' . ($raidGroups->where('id', $character->raid_group_id)->first()->name);
        }

        if (
            isset($showAttendance)
            && $showAttendance
            && !$guild->is_attendance_hidden
        ) {
            if (isset($attendancePercentage) && ((isset($raidCount) && $raidCount) || !isset($raidCount))) {
                $characterMetaText .= ' &sdot; ' . round($attendancePercentage * 100, 2) . "%";
            }

            if (isset($character->raid_count)) {
                $characterMetaText .= ' &sdot; ' . __(':countr', ['count' => $character->raid_count]);
            }

            if (isset($character->benched_count) && $character->benched_count) {
                $characterMetaText .=  ' &sdot; ' . __('benched :countx', ['count' => $character->benched_count]);
            }
        }
    @endphp
    <option value="{{ $character->id }}"
        data-tokens="{{ $character->id }}"
        data-raid-group-id="{{ $character->raid_group_id }}"
        data-name="{{ $character->name }}"
        data-class="text-{{ strtolower($character->class) }}"
        class="js-character-option text-{{ strtolower($character->class) }}-important"
        data-content="<span class='font-weight-medium text-{{ strtolower($character->class) }}-important'>{{ $characterNameText }}</span> <span class='small text-muted'>{{ $characterMetaText }}</span>"
        hack="{{ $character->id }}">
        {{ $characterNameText }} {{ $characterMetaText }}
    </option>
@endforeach
