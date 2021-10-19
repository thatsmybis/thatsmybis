@if (!isset($hideLabel) || !$hideLabel)
    <label for="raid_group_id" class="font-weight-bold">
        <span class="fas fa-fw fa-helmet-battle text-gold"></span>
        {{ __("Main Raid Group") }}
    </label>
@endif
@if ($editRaidGroups)
    <div class="form-group">
        <select name="{{ isset($name) && $name ? $name : 'raid_group_id' }}" class="form-control dark">
            <option value="" selected>
                {{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}
            </option>

            @foreach ($guild->raidGroups as $raidGroup)
                <option value="{{ $raidGroup->id }}"
                    style="color:{{ $raidGroup->getColor() }};"
                    {{ $oldValue ? ($oldValue == $raidGroup->id ? 'selected' : '') : ($character && $character->raidGroup && $character->raidGroup->id == $raidGroup->id ? 'selected' : '') }}>
                    {{ $raidGroup->name }}
                </option>
            @endforeach
        </select>
    </div>
@else
    <div>
        <span class="text-muted">{{ __("locked by the man") }}</span>
    </div>
    <div>
        @if ($character && $character->raidGroup)
            @php
                $raidGroupColor = null;
                if ($character->raidGroup->relationLoaded('role')) {
                    $raidGroupColor = $character->raidGroup->getColor();
                }
            @endphp
            @include('partials/raidGroup', ['raidGroup' => $character->raidGroup])
        @endif
    </div>
@endif
