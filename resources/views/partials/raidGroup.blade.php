@php
    if (!isset($raidGroupName)) {
        $raidGroupName = $raidGroup->name;
    }
@endphp
<span class="tag d-inline text-{{ isset($text) && $text ? $text : '' }}" style="border-color:{{ $raidGroupColor }};"><span class="role-circle" style="background-color:{{ $raidGroupColor }}"></span>
    {{ $raidGroupName }}
</span>
