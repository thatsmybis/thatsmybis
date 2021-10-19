{{--
    @param $oldPrefix string A value to prepend to finding old values... Was added because
                             I didn't see a good way to pass in an iterable set of old values.
--}}
@if (!isset($hideLabel) || !$hideLabel)
    <label for="raid_groups" class="font-weight-bold">
        <span class="fas fa-fw fa-helmet-battle text-muted"></span>
        {{ __("General Raid Groups") }}
    </label>
@endif
<div class="form-group">
    @if ($editRaidGroups)
        <select name="" class="js-input-select form-control dark selectpicker">
            <option value="" selected>
                {{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}
            </option>

            @foreach ($guild->raidGroups as $raidGroup)
                <option value="{{ $raidGroup->id }}"
                    data-tokens="{{ $raidGroup->id }}"
                    style="color:{{ $raidGroup->getColor() }};">
                    {{ $raidGroup->name }}
                </option>
            @endforeach
        </select>

        <ol class="list-inline mt-3">
            @for ($j = 0; $j < $maxRaidGroups; $j++)
                <li class="list-unstyled pt-1 pb-2 pr-2 mt-1 bg-dark rounded"
                    style="{{ old($oldPrefix . 'raid_groups.' . $j) || ($character && $character->secondaryRaidGroups->get($j)) ? '' : 'display:none;' }}">
                    <input type="checkbox" checked
                        name="{{ isset($name) && $name ? $name : 'raid_groups' }}[{{ $j }}]"
                        value="{{ old($oldPrefix . 'raid_groups.' . $j) ? old($oldPrefix . 'raid_groups.' . $j) : ($character && $character->secondaryRaidGroups->get($j) ? $character->secondaryRaidGroups->get($j)->id : '') }}"
                        class="js-input-item"
                        style="display:none;">
                    <button type="button" class="js-input-button close pull-left" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>&nbsp;
                    @php
                        $label = '';
                        $raidGroup = null;
                        if (old($oldPrefix . 'raid_groups.' . $j)) {
                            $raidGroup = $guild->allRaidGroups->where('id', old($oldPrefix . 'raid_groups.' . $j))->first();
                        } else if ($character && $character->secondaryRaidGroups->get($j)) {
                            $raidGroup = $guild->allRaidGroups->where('id',  $character->secondaryRaidGroups->get($j)->id)->first();
                        }

                        if ($raidGroup) {
                            $label = $raidGroup->name;
                        }
                    @endphp
                    <span class="js-input-label">
                        @if ($raidGroup)
                            @php
                                $raidGroupColor = null;
                                if ($raidGroup->relationLoaded('role')) {
                                    $raidGroupColor = $raidGroup->getColor();
                                }
                            @endphp
                            @include('partials/raidGroup', ['text' => 'muted'])
                        @endif
                    </span>
                </li>
            @endfor
        </ol>
    @else
        <div>
            <span class="text-muted">{{ __("locked by the man") }}</span>
        </div>
        <div>
            @if ($character && $character->secondaryRaidGroups->count())
                <ul class="list-inline">
                    @foreach ($character->secondaryRaidGroups as $raidGroup)
                        <li class="list-inline-item">
                            @php
                                $raidGroupColor = null;
                                if ($raidGroup->relationLoaded('role')) {
                                    $raidGroupColor = $raidGroup->getColor();
                                }
                            @endphp
                            @include('partials/raidGroup')
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</div>
