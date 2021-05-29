<li data-val="{{ strtolower($character->name) }}" class="input-item position-relative d-flex bg-light">
    <input hidden name="characters[][character_id]" value="{{ $character->id }}" />
    <div class="js-sort-handle move-cursor text-4 text-unselectable d-flex mr-1">
        <div class="justify-content-center align-self-center">
            <span class="fas fa-fw fa-grip-vertical text-muted"></span>
        </div>
    </div>
    <div class="font-weight-medium {{ $errors->has('characters.' . $loop->index) ? 'text-danger font-weight-bold' : 'text-' . strtolower($character->class) }}">
        <div class="mt-1">
            {{ $character->name }}
            @php
                $raidGroupColor = null;
                if ($character->raidGroup && $character->raidGroup->role) {
                    $raidGroupColor = $character->raidGroup->getColor();
                }
            @endphp
            @if ($character->raidGroup)
                <span class="small tag d-inline font-weight-normal text-muted" style="border-color:{{ $raidGroupColor }};"><span class="role-circle" style="background-color:{{ $raidGroupColor }}"></span>
                    {{ $character->raidGroup->name }}
                </span>
            @endif
        </div>
    </div>
</li>
