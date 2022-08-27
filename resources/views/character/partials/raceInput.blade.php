@if (!isset($hideLabel) || !$hideLabel)
    <label for="race" class="font-weight-normal">
        {{ __("Race") }}
    </label>
@endif
<div class="form-group">
    <select name="{{ isset($name) && $name ? $name : 'race' }}" class="form-control dark">
        <option value="" selected>
            {{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}
        </option>

        @foreach (App\Character::races($guild->expansion_id) as $key => $race)
            <option value="{{ $key }}" class="text-{{ slug($race['faction']) }}-important font-weight-medium"
                {{ $oldValue ? ($oldValue == $key ? 'selected' : '') : ($character && $character->race == $key ? 'selected' : '') }}>
                {{ $race['name'] }}
            </option>
        @endforeach
    </select>
</div>
