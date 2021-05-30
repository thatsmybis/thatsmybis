<li data-val="{{ strtolower($character->name) }}" class="d-flex input-item position-relative d-flex flex-row text-unselectable bg-light">
    <div class="js-sort-handle move-cursor d-flex w-100">
        <input hidden name="characters[][character_id]" value="{{ $character->id }}" />
        <div class="text-4 text-unselectable d-flex mr-1">
            <div class="justify-content-center align-self-center">
                <span class="fas fa-fw fa-grip-vertical text-muted"></span>
            </div>
        </div>
        <div class=" w-100 font-weight-medium {{ $errors->has('characters.' . $loop->index) ? 'text-danger font-weight-bold' : 'text-' . strtolower($character->class) }}">
            <div class="mt-1">
                {{ $character->name }}
                @php
                    $raidGroupColor = null;
                    if ($character->raidGroup && $character->raidGroup->role) {
                        $raidGroupColor = $character->raidGroup->getColor();
                    }
                @endphp
                @if ($character->raidGroup)
                    <div class="small tag d-inline font-weight-normal text-muted float-right" style="border-color:{{ $raidGroupColor }};"><span class="role-circle align-fix" style="background-color:{{ $raidGroupColor }}"></span>
                        {{ $character->raidGroup->name }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</li>
