@if (isset($attendancePercentage))
    <span class="{{ getAttendanceColor($attendancePercentage) }}" title="attendance">{{ round($attendancePercentage * 100, 2) }}%</span>
@endif
@if (isset($raidCount))
    <span class="{{ isset($smallRaid) && $smallRaid ? 'small' : ''}} text-muted">{{ $raidCount }} raids</span>
@endif
