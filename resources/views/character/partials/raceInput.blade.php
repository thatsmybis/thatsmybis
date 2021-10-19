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
            <option value="{{ $key }}"
                {{ $oldValue ? ($oldValue == $key ? 'selected' : '') : ($character && $character->race == $key ? 'selected' : '') }}>
                {{ $race }}
            </option>
        @endforeach
    </select>
</div>
