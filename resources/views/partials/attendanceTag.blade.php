@if (isset($attendancePercentage))
    <span class="{{ getAttendanceColor($attendancePercentage) }}" title="attendance">{{ round($attendancePercentage * 100, 2) }}%</span>
@endif
@if (isset($raidCount))
    <span class="{{ isset($smallRaid) && $smallRaid ? 'small' : ''}} text-muted" title="{{ $raidCount }} raids attended">
        @if (isset($raidShort) && $raidShort)
            +{{ $raidCount }}
        @else
            {{ $raidCount }} raids
        @endif
    </span>
@endif
