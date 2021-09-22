<ul class="list-inline">
    @if (isset($attendancePercentage) && ((isset($raidCount) && $raidCount) || !isset($raidCount)))
        <li class="list-inline-item {{ getAttendanceColor($attendancePercentage) }}" title="attendance">
            {{ round($attendancePercentage * 100, 2) }}%
        </li>
    @endif
    @if (isset($raidCount))
        <li class="list-inline-item {{ isset($smallRaid) && $smallRaid ? 'small' : ''}} text-muted" title="{{ $raidCount }} raids attended">
            @if (isset($raidShort) && $raidShort)
                {{ $raidCount }}r
            @else
                {{ $raidCount }} {{ __("raids") }}
            @endif
        </li>
    @endif
    @if (isset($benchedCount) && $benchedCount)
        <li class="list-inline-item {{ isset($smallRaid) && $smallRaid ? 'small' : ''}} text-muted" title="{{ __('benched :count times', ['count' => $benchedCount]) }}">
            {{ __("benched :countx", ['count' => $benchedCount]) }}
        </li>
    @endif
</ul>
