@php
    if (isset($item) && $item) {
        $oldIsReceived = old($name . '.' . $i . '.is_received');
        $oldIsOffspec  = old($name . '.' . $i . '.is_offspec');
    } else {
        $item          = null;
        $oldIsReceived = false;
        $oldIsOffspec  = false;
    }
@endphp
<ul class="list-inline">
    <li class="list-inline-item">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="{{ $name }}[{{ $index }}][is_received]" value="1" class="" autocomplete="off"
                    {{ $oldIsReceived && $oldIsReceived == 1 ? 'checked' : ($item && $item->pivot->is_received ? 'checked' : '') }}>
                    Received
            </label>
        </div>
    </li>
    <li class="list-inline-item">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="{{ $name }}[{{ $index }}][is_offspec]" value="1" class="" autocomplete="off"
                    {{ $oldIsOffspec && $oldIsOffspec == 1 ? 'checked' : ($item && $item->pivot->is_offspec ? 'checked' : '') }}>
                    OS
            </label>
        </div>
    </li>
</ul>
