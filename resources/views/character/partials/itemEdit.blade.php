@php
    $oldIsReceived = false;
    $oldIsOffspec  = false;
    $oldReceivedAt = null;
    $oldRaidId     = null;

    if (isset($item) && $item) {
        $oldIsReceived = old($name . '.' . $i . '.is_received');
        $oldIsOffspec  = old($name . '.' . $i . '.is_offspec');
        $oldReceivedAt = old($name . '.' . $i . '.new_received_at');
        $oldRaidId     = old($name . '.' . $i . '.new_raid_id');
    } else {
        $item = null;
    }
@endphp
<ul class="list-inline">
    @if ($name != 'received')
        <li class="list-inline-item">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="{{ $name }}[{{ $index }}][is_received]" value="1" class="" autocomplete="off"
                        {{ $oldIsReceived && $oldIsReceived == 1 ? 'checked' : ($item && $item->pivot->is_received ? 'checked' : '') }}>
                        Received
                </label>
            </div>
        </li>
    @endif
    <li class="list-inline-item">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="{{ $name }}[{{ $index }}][is_offspec]" value="1" class="" autocomplete="off"
                    {{ $oldIsOffspec && $oldIsOffspec == 1 ? 'checked' : ($item && $item->pivot->is_offspec ? 'checked' : '') }}>
                    OS
            </label>
        </div>
    </li>
    @if ($name == 'received')
        <li class="list-inline-item">
            <label for="{{ $name }}[{{ $index }}][date_input]" class="sr-only font-weight-light">
                Date
            </label>
            <input class="js-date" type="text" name="{{ $name }}[{{ $index }}][new_received_at]" hidden value="{{ $oldReceivedAt ? $oldReceivedAt : '' }}">
            <input value="" min="2004-09-22" max="{{ $maxDate }}" type="date" placeholder="leave blank to keep existing" class="js-date-input form-control dark slim-date" autocomplete="off">
        </li>
        <li class="list-inline-item">
            <label for="{{ $name }}[{{ $index }}][new_raid_id]" class="sr-only font-weight-light">
                Raid
            </label>
            <select name="{{ $name }}[{{ $index }}][new_raid_id]"
                    class="slim-select form-control dark {{ $errors->has($name . '.' . $i . '.new_raid_id') ? 'form-danger' : '' }}"
                    data-live-search="true" autocomplete="off">
                <option value="" class="text-muted-important">
                    change raid
                </option>

                {{-- See the notes at the top for why the options look like this --}}
                @if ($oldRaidId)
                    @php
                        // Select the correct option
                        $options = str_replace('hack="' . $oldRaidId . '"', 'selected', $raidSelectOptions);
                     @endphp
                     {!! $options !!}
                @else
                    {!! $raidSelectOptions !!}
                @endif
            </select>
        </li>
    @endif
</ul>
